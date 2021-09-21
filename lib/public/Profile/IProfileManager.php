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

use OCP\Accounts\IAccountManager;
use OCP\IUser;

/**
 * @since 23.0.0
 */
interface IProfileManager {

	/**
	 * Array of account properties displayed on the profile
	 *
	 * @since 23.0.0
	 */
	public const PROFILE_PROPERTIES = [
		IAccountManager::PROPERTY_DISPLAYNAME,
		IAccountManager::PROPERTY_ADDRESS,
		IAccountManager::PROPERTY_ORGANISATION,
		IAccountManager::PROPERTY_ROLE,
		IAccountManager::PROPERTY_HEADLINE,
		IAccountManager::PROPERTY_BIOGRAPHY,
	];

	/**
	 * Map of account properties to camelCase variants
	 *
	 * @since 23.0.0
	 */
	public const PROFILE_PROPERTY_MAP = [
		IAccountManager::PROPERTY_DISPLAYNAME => 'displayName',
		IAccountManager::PROPERTY_ADDRESS => 'address',
		IAccountManager::PROPERTY_ORGANISATION => 'organisation',
		IAccountManager::PROPERTY_ROLE => 'role',
		IAccountManager::PROPERTY_HEADLINE => 'headline',
		IAccountManager::PROPERTY_BIOGRAPHY => 'biography',
	];

	/**
	 * Returns whether the user account property is visible to the visiting user
	 * based on it's scope
	 *
	 * @since 23.0.0
	 */
	public function isPropertyVisible(IUser $user, IUser|null $visitingUser, string $property): bool;

	/**
	 * Returns the profile parameters in an
	 * associative array
	 *
	 * @since 23.0.0
	 */
	public function getProfileParams(IUser $user, IUser|null $visitingUser): array;
}
