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

namespace Invy55\Sponges\Tasks;

use pocketmine\scheduler\Task;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\block\Block;

class setBlockTask extends Task{
    public function __construct($that, $level, $vector, $block, $firstP, $secondP){
        $this->plugin = $that;
        $this->level = $level;
        $this->vector = $vector;
        $this->block = $block;
        $this->fP = $firstP;
        $this->sP = $secondP;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currentTick){
        $this->level->addParticle(new DestroyBlockParticle($this->vector, Block::get(Block::STONE,0)));
        $this->level->setBlock($this->vector, $this->block, $this->fP, $this->sP);
    }
}
