<?php

namespace Doma\LoginPM5;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\console\ConsoleCommandSender;


class Main extends PluginBase implements Listener {

    private $players;
    private $config;

    public function onEnable(): void {
        $this->players = [];
        $this->saveResource("players/example.yml");
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $this->config = new Config($this->getDataFolder(). "config.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->getLogger()->info("LoginPM5 Plugin Enabled");
    }

    public function onDisable(): void {

        $this->getLogger()->info("LoginPM5 Plugin Disabled");
    }


    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $name = $event->getPlayer()->getName();
        $config = new Config($this->getDataFolder(). "players/$name.yml", Config::YAML);
        if($config->exists("password")){
            $this->loginForm($event->getPlayer());
        } else {
            $this->registerForm($event->getPlayer());
        }
    }

    public function registerForm(Player $player): void {
        $form = new CustomForm(function(Player $player, ?array $data): void {
            if ($data === null) {
                $player->kick($this->config->get("password_mismatch_kick_message"), false);
                return;
            }
            $password = $data[0];
            $confirmPassword = $data[1];
            $name = $player->getName();
            $config = new Config($this->getDataFolder(). "players/$name.yml", Config::YAML);

            if ($password === $confirmPassword) {
                $config->set("password", $password);
                $config->set("LoginStreak", 0);
                $config->save();
                $this->checkLoginRewards($player, 1);
                $player->sendMessage($this->config->get("register_success_message"));
            } else {
                $player->kick($this->config->get("password_mismatch_kick_message"), false);
            }
        });

        $form->setTitle($this->config->get("register_title"));
        $form->addInput("Enter a password", "Example: pw123");
        $form->addInput("Confirm password", "Example: pw123");

        $player->sendForm($form);
    }

    public function loginForm(Player $player): void {

        $form = new CustomForm(function(Player $player, ?array $data) : void {
            if ($data === null) {
                $player->kick(TextFormat::RED . "Login cancelled", false);
                return;
            }
            $password = $data[0];
            $name = $player->getName();
            $config = new Config($this->getDataFolder(). "players/$name.yml", Config::YAML);
            if ($config->exists("password")) {
                $savedPassword = $config->get("password");
            } else {
                return;
            }

            if ($password === $savedPassword) {
                $player->sendMessage($this->config->get("login_success_message"));
                $streak = $config->get("LoginStreak");
                $config->set("LoginStreak", $streak + 1);
                $loginStreak = $streak + 1;

                $this->checkLoginRewards($player, $streak);
                $config->save();
                $loginTarget = $this->config->get("login_target", 5);
                $remainingLogins = $loginTarget - ($loginStreak % $loginTarget);
                $player->sendMessage("You have logged in $loginStreak times. Log in $remainingLogins more times to receive a reward.");
            } else {
                $player->kick($this->config->get("invalid_password_kick_message"), false);
            }
        });

        $form->setTitle($this->config->get("login_title"));
        $form->addInput("Enter your password", "Example: pw123");

        $player->sendForm($form);
    }

    private function checkLoginRewards(Player $player, int $loginStreak): void {
        $loginRewardsEnabled = $this->config->get("login-rewards", false);
        if (!$loginRewardsEnabled) {
            return;
        }

        $rewardsConfig = $this->config->get("login-reward", []);
        foreach ($rewardsConfig as $milestone => $command) {
            if ($loginStreak >= $milestone) {
                $command = str_replace("{player}", $player->getName(), $command);
                $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), $command);

                $rewardMessage = str_replace("{milestone}", (string)$milestone, $this->config->get("reward_message"));
                $player->sendMessage($rewardMessage);
            }
        }
    }

    public function unRegister($name){
        $config = new Config($this->getDataFolder(). "players/$name.yml", Config::YAML);
        if($config->exists("password")){
            $config->remove("password");
            return "Unregister $name was successful";
        } else {
            return "$name was never conected";
        }
    }

    public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool
    {
        if(count($args) != 1){
            $player->sendMessage("Usage: /unregister <player>");
            return true;
        }
        $player->sendMessage($this->unRegister($args[0]));
        return true;
    }

}
