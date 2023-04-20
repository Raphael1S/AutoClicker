<?php

namespace Raphael\Hit;

# https://github.com/Raphael1S/AutoClicker

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\math\AxisAlignedBB;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Position;
use pocketmine\utils\Config;
require_once("Update.php");

class Blaze extends PluginBase implements Listener {

    /** @var int[] */
    private $taskIds = [];


    public function onEnable() {
        $pluginName = $this->getDescription()->getName();
        $pluginVersion = $this->getDescription()->getVersion();
        atualizarPlugin($this, $pluginName, $pluginVersion);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
                $this->saveResource("config.yml");
                $config = yaml_parse_file($this->getDataFolder() . "config.yml");
        $this->configmundos = $config["Mundos_permitidos"];
        $this->configperm = $config["Permissão"];
        $this->msg1 = $config["Mensagem_autoclicker_habilitado"];
        $this->msg2 = $config["Mensagem_autoclicker_desativado"];
        $this->msg3 = $config["Mensagem_sem_permissao"];
        $this->msg4 = $config["Mensagem_mundo_nao_permitido"];
        $this->msg5 = $config["Mensagem_mudou_mundo"];
        $this->tempo = $config["Tempo"] * 20;
        $this->getLogger()->info("§eAutoClicker habilitado! @ Raphael S.");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if ($cmd->getName() === "autoclicker") {
            if ($sender instanceof Player) {
                if (!$sender->hasPermission($this->configperm)) {
                    $sender->sendMessage($this->msg3);
                    return false;
                }

                if (!in_array($sender->getLevel()->getName(), $this->configmundos)) {
                    $sender->sendMessage($this->msg4);
                    return false;
                }

                if (isset($args[0]) && $args[0] === "ver") {
                    if (isset($args[1])) {
                        $targetPlayer = $this->getServer()->getPlayer($args[1]);
                        if ($targetPlayer instanceof Player) {
                            $autoclickerActive = isset($this->taskIds[$targetPlayer->getName()]);
                            $sender->sendMessage($targetPlayer->getName() . " está " . ($autoclickerActive ? "com" : "sem") . " autoclicker ativado.");
                            return true;
                        } else {
                            $sender->sendMessage(TextFormat::RED . "O jogador especificado não está online.");
                            return false;
                        }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "Uso correto: /autoclicker ver <player>");
                        return false;
                    }
                }

                if (isset($this->taskIds[$sender->getName()])) {
                    $this->getScheduler()->cancelTask($this->taskIds[$sender->getName()]);
                    unset($this->taskIds[$sender->getName()]);
                    $sender->sendMessage($this->msg2);
                } else {
                    $this->hitarMobsAoRedor($sender);
                    $sender->sendMessage($this->msg1);
                }
                return true;
            } else {
                $sender->sendMessage(TextFormat::RED . "Este comando só pode ser usado por jogadores.");
                return false;
            }
        }
        return true;
    }


    public function hitarMobsAoRedor(Player $player) {
        $taskId = $this->getScheduler()->scheduleRepeatingTask(new HitarTask($player), $this->tempo)->getTaskId();
        $this->taskIds[$player->getName()] = $taskId;
    }

public function onPlayerQuit(PlayerQuitEvent $event) {
     $playerName = $event->getPlayer()->getName();
     if (array_key_exists($playerName, $this->taskIds)) {
         $taskId = $this->taskIds[$playerName];
         $this->getScheduler()->cancelTask($taskId);
         unset($this->taskIds[$playerName]);
     }
 }
    
public function onEntityLevelChange(EntityLevelChangeEvent $event): void {
$entity = $event->getEntity();
if ($entity instanceof Player) {
$oldLevel = $event->getOrigin()->getName();
$newLevel = $event->getTarget()->getName();
    if (!in_array($newLevel, $this->configmundos)) {
        if (isset($this->taskIds[$entity->getName()])) {
            $taskId = $this->taskIds[$entity->getName()];
            $this->getScheduler()->cancelTask($taskId);
            unset($this->taskIds[$entity->getName()]);
$entity->sendMessage($this->msg5);
        }
    }
}
}
}

class HitarTask extends \pocketmine\scheduler\Task {
    
    private $player;
    private $lastX;
    private $lastY;
    private $lastZ;
    private $lastRadius;
    private $hitEntities;
    
    public function __construct(Player $player) {
        $this->player = $player;
        $this->lastX = $player->getX();
        $this->lastY = $player->getY();
        $this->lastZ = $player->getZ();
        $this->lastRadius = 0;
        $this->hitEntities = [];
    }
    
public function onRun(int $currentTick) {
    $x = $this->player->getX();
    $y = $this->player->getY();
    $z = $this->player->getZ();
    $radius = 1;

    // Se o jogador se moveu ou o raio mudou, resetar as entidades atacadas
    if ($x != $this->lastX || $y != $this->lastY || $z != $this->lastZ || $radius != $this->lastRadius) {
        $this->hitEntity = null;
    }

    $this->lastX = $x;
    $this->lastY = $y;
    $this->lastZ = $z;
    $this->lastRadius = $radius;

    // Se já estiver atacando uma entidade, continue atacando-a
    if ($this->hitEntity instanceof Entity) {
        if ($this->player->distance($this->hitEntity) <= 1) {
            $this->hit($this->hitEntity);
            return;
        } else {
            $this->hitEntity = null;
        }
    }

    // Procurar uma nova entidade para atacar
    $bb = new AxisAlignedBB($x - $radius, $y - $radius, $z - $radius, $x + $radius, $y + $radius, $z + $radius);
    if ($this->player->getLevel() !== null) {
        $entities = $this->player->getLevel()->getNearbyEntities($bb, null, function(Entity $entity): bool {
            return $entity instanceof \pocketmine\entity\Monster && $this->player->distance($entity) <= 1;
        });
        foreach ($entities as $entity) {
            // Verificar se o jogador está olhando para a entidade
            $direction = $this->player->getDirectionVector();
            $dotProduct = $entity->asVector3()->subtract($this->player->asVector3())->normalize()->dot($direction);
            if ($dotProduct > 0.5) {
                $this->hitEntity = $entity;
                $this->hit($this->hitEntity);
                break;
            }
        }
    }
}

    
    public function hit(Entity $entity, $damage = 1) {
        $entity->attack(new EntityDamageByEntityEvent($this->player, $entity, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage));
    }
}
