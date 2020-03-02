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

class absorbWaterTask extends Task{
    public function __construct($that, $position){
        $this->plugin = $that;
        $this->position = $position;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function onRun($currentTick){
        $this->getPlugin()->absorbWater($this->position);
    }
}
