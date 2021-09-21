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

namespace OCP\Profile;

use OC\Profile\Actions\EmailAction;
use OC\Profile\Actions\PhoneAction;
use OC\Profile\Actions\TwitterAction;
use OC\Profile\Actions\WebsiteAction;
use OCP\IUser;
use OCP\Profile\IAction;

/**
 * @since 23.0.0
 */
interface IActionManager {

	/**
	 * Array of account property action classes
	 *
	 * @since 23.0.0
	 */
	public const ACCOUNT_PROPERTY_ACTION_QUEUE = [
		EmailAction::class,
		PhoneAction::class,
		WebsiteAction::class,
		TwitterAction::class,
	];

	/**
	 * Queue an action for registration
	 *
	 * @since 23.0.0
	 */
	public function queueAction(string $actionClass): void;

	/**
	 * Returns an array of registered profile actions for the user
	 *
	 * @return IAction[]
	 *
	 * @since 23.0.0
	 */
	public function getActions(IUser $user, IUser|null $visitingUser): array;
}
