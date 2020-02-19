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
namespace Invy55\Sponges;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\level\Position;

class Sponge extends Transparent{

protected $id = self::SPONGE;

    public function __construct(){
    }

	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        if($this->getDamage() == 0){
            if(self::absorbWater(new Position($this->x, $this->y, $this->z, $this->getLevel()))){
                return $this->getLevel()->setBlock($this, Block::get(Block::SPONGE, 1), true, true);
            }else{
                return $this->getLevel()->setBlock($this, $this, true, true);
            }
        }else{
            return $this->getLevel()->setBlock($this, $this, true, true);
        }
    }
    
    public function getName() : string{
        return "Sponge";
    }
    private function absorbWater(Position $center){
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
