<?php
/*
8888888                            888888888  888888888  
  888                              888        888        
  888                              888        888        
  888   88888b.  888  888 888  888 8888888b.  8888888b.  
  888   888 "88b 888  888 888  888      "Y88b      "Y88b 
  888   888  888 Y88  88P 888  888        888        888 
  888   888  888  Y8bd8P  Y88b 888 Y88b  d88P Y88b  d88P 
8888888 888  888   Y88P    "Y88888  "Y8888P"   "Y8888P"  
                               888                       
                          Y8b d88P                       
                           "Y88P"
----- This project is under the GNU Affero General Public License v3.0 -----                       
*/
declare(strict_types=1);

namespace Invy55\Sponges;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskScheduler;
use Invy55\Sponges\Tasks\setBlockTask;
use Invy55\Sponges\Tasks\absorbWaterTask;
use pocketmine\block\Block;

class Main extends PluginBase implements Listener{
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    
    public function onBlockPlaceEvent(BlockPlaceEvent $event){
        $block = $event->getBlock();
        if($block->getDamage() == 0 and $block->getId() == 19){
            if(self::absorbWater(new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel()))){
                $this->getScheduler()->scheduleDelayedTask(new setBlockTask($this, $block->getLevel(), new Vector3($block->getX(), $block->getY(), $block->getZ()), Block::get(Block::SPONGE, 1), true, true), 1);
                return;
            }
        }
    }

    public function onWaterFlow(BlockSpreadEvent $event){
        $source = $event->getSource();
        $block = $event->getBlock();
       
        if($source->getId() == 8){
            $sponge = self::hasSpongeNear($block->getLevel(), $block->getX(), $block->getY(), $block->getZ());
            if($sponge instanceof Block){
                $this->getScheduler()->scheduleDelayedTask(new absorbWaterTask($this, new Position($sponge->getX(), $sponge->getY(), $sponge->getZ(), $sponge->getLevel())), 1);
                $this->getScheduler()->scheduleDelayedTask(new setBlockTask($this, $sponge->getLevel(), new Vector3($sponge->getX(), $sponge->getY(), $sponge->getZ()), Block::get(Block::SPONGE, 1), true, true), 1);
                return;
            }
        }
    }
    
    public function onBucketUse(PlayerBucketEmptyEvent $event){
        $block = $event->getBlockClicked();
        $sponge = self::hasSpongeNear($block->getLevel(), $block->getX(), $block->getY(), $block->getZ());
        if($sponge instanceof Block){
            $this->getScheduler()->scheduleDelayedTask(new absorbWaterTask($this, new Position($sponge->getX(), $sponge->getY(), $sponge->getZ(), $sponge->getLevel())), 1);
            $this->getScheduler()->scheduleDelayedTask(new setBlockTask($this, $sponge->getLevel(), new Vector3($sponge->getX(), $sponge->getY(), $sponge->getZ()), Block::get(Block::SPONGE, 1), true, true), 1);
            return;
        }
    }

    public function hasSpongeNear($world, $xBlock, $yBlock, $zBlock){
        for($x = -1; $x <= 1; ++$x){
            for($y = -1; $y <= 1; ++$y){
                for($z = -1; $z <= 1; ++$z){
                    $block = $world->getBlockAt($xBlock + $x, $yBlock + $y, $zBlock + $z);
                    if($block->getId() == 19 and $block->getDamage() == 0){
                        return $block;
                    }
                    
                }
            }
        }
        return false;
    }
    
    public function absorbWater(Position $center){
        $world = $center->getLevel();
        $waterRemoved = 0;
        $yBlock = $center->getY();
        $zBlock = $center->getZ();
        $xBlock = $center->getX();
        $radius = 5;
        $l = false;
        $touchingWater = false;
        for($x = -1; $x <= 1; ++$x){
            for($y = -1; $y <= 1; ++$y){
                for($z = -1; $z <= 1; ++$z){
                    $block = $world->getBlockAt($xBlock + $x, $yBlock + $y, $zBlock + $z);
                    if($block->getId() == 9 || $block->getId() == 8){
                        $touchingWater = true;
                    }
                }
            }
        }
        if($touchingWater){
            for ($x = $center->getX()-$radius; $x <= $center->getX()+$radius; $x++) {
                $xsqr = ($center->getX()-$x) * ($center->getX()-$x);
                for ($y = $center->getY()-$radius; $y <= $center->getY()+$radius; $y++) {
                    $ysqr = ($center->getY()-$y) * ($center->getY()-$y);
                    for ($z = $center->getZ()-$radius; $z <= $center->getZ()+$radius; $z++) {
                        $zsqr = ($center->getZ()-$z) * ($center->getZ()-$z);
                        if(($xsqr + $ysqr + $zsqr) <= ($radius*$radius)) {
                            if($y > 0) {
                                $level = $center->getLevel();
                                if($level->getBlockAt($x,$y,$z)->getId() == 9 || $level->getBlockAt($x,$y,$z)->getId() == 8){
                                    $l = true;
                                    $level->setBlock(new Vector3($x, $y, $z), Block::get(0,0));
                                }

                            }    
                        }
                    }
                }
            }
        }
        return $l;
    }
}
