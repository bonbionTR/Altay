<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\permission;

use pocketmine\lang\KnownTranslationParameterInfo;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissionNames as Names;
use pocketmine\utils\AssumptionFailedError;
use function preg_last_error_msg;
use function preg_replace;

abstract class DefaultPermissions{
	public const ROOT_CONSOLE = Names::GROUP_CONSOLE;
	public const ROOT_OPERATOR = Names::GROUP_OPERATOR;
	public const ROOT_USER = Names::GROUP_USER;

	/**
	 * @param Permission[] $grantedBy
	 * @param Permission[] $deniedBy
	 */
	public static function registerPermission(Permission $candidate, array $grantedBy = [], array $deniedBy = []) : Permission{
		foreach($grantedBy as $permission){
			$permission->addChild($candidate->getName(), true);
		}
		foreach($deniedBy as $permission){
			$permission->addChild($candidate->getName(), false);
		}
		PermissionManager::getInstance()->addPermission($candidate);

		return PermissionManager::getInstance()->getPermission($candidate->getName());
	}

	/**
	 * @param Permission[] $grantedBy
	 */
	private static function registerNoArgsDesc(string $permission, array $grantedBy) : Permission{
		$translationKey = preg_replace("/^pocketmine\./", "pocketmine.permission.", $permission) ?? throw new AssumptionFailedError(preg_last_error_msg());
		$parameters = KnownTranslationParameterInfo::TABLE[$translationKey] ?? null;
		if($parameters === null){
			throw new \InvalidArgumentException("Expected translation key $translationKey not defined");
		}
		if(count($parameters) !== 0){
			throw new \InvalidArgumentException("Cannot use this function to register a permission with a parameterisable description string");
		}
		$translatable = new Translatable($translationKey);
		return self::registerPermission(new Permission($permission, $translatable), $grantedBy);
	}

	public static function registerCorePermissions() : void{
		$consoleRoot = self::registerNoArgsDesc(self::ROOT_CONSOLE, []);
		$operatorRoot = self::registerNoArgsDesc(self::ROOT_OPERATOR, [$consoleRoot]);
		$everyoneRoot = self::registerNoArgsDesc(self::ROOT_USER, [$operatorRoot]);

		self::registerNoArgsDesc(Names::BROADCAST_ADMIN, [$operatorRoot]);
		self::registerNoArgsDesc(Names::BROADCAST_USER, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_BAN_IP, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_BAN_LIST, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_BAN_PLAYER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_CLEAR_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_CLEAR_SELF, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_DEFAULTGAMEMODE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_DIFFICULTY, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_DUMPMEMORY, [$consoleRoot]);
		self::registerNoArgsDesc(Names::COMMAND_EFFECT_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_EFFECT_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_ENCHANT_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_ENCHANT_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_GAMEMODE_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_GAMEMODE_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_GC, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_GIVE_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_GIVE_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_HELP, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_KICK, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_KILL_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_KILL_SELF, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_LIST, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_ME, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_OP_GIVE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_OP_TAKE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_PARTICLE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_PLUGINS, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SAVE_DISABLE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SAVE_ENABLE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SAVE_PERFORM, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SAY, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SEED, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SETWORLDSPAWN, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SPAWNPOINT_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_SPAWNPOINT_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_STATUS, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_STOP, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TELEPORT_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TELEPORT_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TELL, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TIME_ADD, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TIME_QUERY, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TIME_SET, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TIME_START, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TIME_STOP, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TIMINGS, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TITLE_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TITLE_SELF, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_TRANSFERSERVER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_UNBAN_IP, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_UNBAN_PLAYER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_VERSION, [$everyoneRoot]);
		self::registerNoArgsDesc(Names::COMMAND_WHITELIST_ADD, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_WHITELIST_DISABLE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_WHITELIST_ENABLE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_WHITELIST_LIST, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_WHITELIST_RELOAD, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_WHITELIST_REMOVE, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_XP_OTHER, [$operatorRoot]);
		self::registerNoArgsDesc(Names::COMMAND_XP_SELF, [$operatorRoot]);
	}
}
