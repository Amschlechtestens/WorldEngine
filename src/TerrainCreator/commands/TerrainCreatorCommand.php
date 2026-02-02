<?php

declare(strict_types=1);

namespace TerrainCreator\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use TerrainCreator\Main;
use TerrainCreator\ui\MainMenuForm;

class TerrainCreatorCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct(
            "terraincreator",
            "Open the Terrain Creator menu",
            "/terraincreator",
            ["tc", "terraingen"]
        );
        $this->setPermission("terraincreator.use");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cDieser Command kann nur ingame verwendet werden!");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $form = new MainMenuForm($this->plugin);
        $sender->sendForm($form);
        return true;
    }
}
