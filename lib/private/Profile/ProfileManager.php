<?php

declare(strict_types=1);

/**
 * @copyright 2021 Christopher Ng <chrng8@gmail.com>
 *
 * @author Christopher Ng <chrng8@gmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OC\Profile;

use OC\KnownUser\KnownUserService;
use OCP\Accounts\IAccountManager;
use OCP\Accounts\IAccountProperty;
use OCP\Accounts\PropertyDoesNotExistException;
use OCP\IUser;
use OCP\Profile\IAction;
use OCP\Profile\IProfileManager;
use Psr\Log\LoggerInterface;
use OCP\Profile\IActionManager;

/**
 * @inheritDoc
 */
class ProfileManager implements IProfileManager {

	/** @var IAccountManager */
	private $accountManager;

	/** @var IActionManager */
	private $actionManager;

	/** @var KnownUserService */
	private $knownUserService;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		IAccountManager $accountManager,
		IActionManager $actionManager,
		KnownUserService $knownUserService,
		LoggerInterface $logger
	) {
		$this->accountManager = $accountManager;
		$this->actionManager = $actionManager;
		$this->knownUserService = $knownUserService;
		$this->logger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function isPropertyVisible(IUser $user, IUser|null $visitingUser, string $property): bool {
		try {
			$account = $this->accountManager->getAccount($user);
			$scope = $account->getProperty($property)->getScope();

			// Users, guests, and public access (non-logged in) visitors may only view profiles on the same server
			// Handle scope so that properties are only visible to visiting users who are permitted
			// 1) Private   - hidden from public access and from unknown users
			// 2) Local     - hidden from nobody
			// 3) Federated - hidden from nobody
			// 4) Published - hidden from nobody
			switch ($scope) {
				case IAccountManager::SCOPE_PRIVATE:
					// visiting user is null when not logged in
					return $visitingUser !== null && $this->knownUserService->isKnownToUser($user->getUID(), $visitingUser->getUID());
				case IAccountManager::SCOPE_LOCAL:
					return true;
				case IAccountManager::SCOPE_FEDERATED:
					return true;
				case IAccountManager::SCOPE_PUBLISHED:
					return true;
				default:
					return false;
			}
		} catch (PropertyDoesNotExistException $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getProfileParams(IUser $user, IUser|null $visitingUser): array {
		$account = $this->accountManager->getAccount($user);
		// Initialize associative array of profile parameters
		$profileParameters = [
			'userId' => $account->getUser()->getUID(),
		];

		// TODO add additional emails
		$additionalEmails = array_map(
			function (IAccountProperty $property) {
				return $property->getValue();
			},
			$account->getPropertyCollection(IAccountManager::COLLECTION_EMAIL)->getProperties(),
		);

		// Add account property parameters
		foreach (IProfileManager::PROFILE_PROPERTIES as $property) {
			$profileParameters[IProfileManager::PROFILE_PROPERTY_MAP[$property]] =
				$this->isPropertyVisible($user, $visitingUser, $property)
				// If empty string explicitly set to null
				? $account->getProperty($property)->getValue() || null
				: null;
		}

		// Add avatar visibility parameter
		$profileParameters['isUserAvatarVisible'] = $this->isPropertyVisible($user, $visitingUser, IAccountManager::PROPERTY_AVATAR);

		// Add actions paraemer
		$profileParameters['actions'] = array_map(
			function (IAction $action) {
				return [
					'id' => $action->getId(),
					'icon' => $action->getIcon(),
					'title' => $action->getTitle(),
					'label' => $action->getLabel(),
					'target' => $action->getTarget(),
				];
			},
			$this->actionManager->getActions($user, $visitingUser),
		);

		return $profileParameters;
	}
}
