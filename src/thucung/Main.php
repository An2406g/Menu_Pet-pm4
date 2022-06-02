<?php 

namespace thucung;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

use pocketmine\utils\{Config, TextFormat};
use onebone\economyapi\EconomyAPI;
use jojoe77777\FormAPI;

Class Main extends PluginBase implements Listener{
    
    public $playerList = [];
    
    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info("§aĐã kích hoạt giao diện pet, by ngminhphap aka rbplugin");
        
        $this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->coin = $this->getServer()->getPluginManager()->getPlugin("CoinAPI");
        }
        
    public function onDisable():void{
        $this->getServer()->getLogger()->info("§cĐã tắt giao diện pet, by ngminhphap aka rbplugin");
        }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        if($cmd->getName() == "thucung"){
            $this->giaodien($sender);
            } 
       return true; 
       }
    
    public function giaodien(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                return;
            }
            switch($result){
                case 0:
                $this->quanli($sender);
                break;
                case 1:
                $this->muapet($sender);
                break;
                case 2:
                $command = "listpets";
                $this->getServer()->getCommandMap()->dispatch($sender, $command);
                break;
            }
        });
        $coin = $this->coin->myCoin($sender);
        $money = $this->eco->myMoney($sender);
        $form->setTitle("§lGIAO DIỆN THÚ CƯNG");
        $form->setContent("§7[§e➸§7]§7 Tiền của bạn: §e" . $money.", §7coin của bạn:§e ".$coin);
        $form->addButton("§l§c• §9QUẢN LÝ THÚ CƯNG §c•\n§r§8Nhấn để xem",1,"https://cdn-icons-png.flaticon.com/512/784/784138.png");
        $form->addButton("§l§c• §9CỬA HÀNG THÚ CƯNG §c•\n§r§8Nhấn để xem",1,"https://cdn-icons-png.flaticon.com/512/6915/6915554.png");
        $form->addButton("§l§c• §9DANH SÁCH THÚ CƯNG§c•\n§r§8Nhấn để xem",1,"https://i.pinimg.com/564x/2e/75/1b/2e751b35c5ab41c5986056f8fe5084bc.jpg");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function quanli(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->giaodien($sender);
                return;
            }
            switch($result){
                case 0:
                $this->giaodien($sender);
                break;
                case 1:
                $this->goipet($sender);
                break;
                case 2:
                $this->doitenpet($sender);
                break;
            }
        });
        $form->setTitle("§lQUẢN LÝ THÚ CƯNG");
        $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
        $form->addButton("§l§c•§9 BẬT/TẮT THÚ CƯNG §c•\n§r§8Nhấn để chỉnh",1,"https://cdn-icons-png.flaticon.com/512/1488/1488531.png");
        $form->addButton("§l§c•§9 ĐỔI TÊN THÚ CƯNG §c•\n§r§8Nhấn để đổi",1,"https://cdn-icons-png.flaticon.com/512/1733/1733385.png");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function goipet($sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $sender, $data){
            $result = $data;
            if($data === null){
                return $this->giaodien($sender);
           }
           $command = "togglepet $data[0]";
           $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§lBẬT/TẮT THÚ CƯNG");
        $form->addInput("\n§7[§e➸§7] §eLƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function doitenpet($sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function(Player $sender, $data){
            $result = $data;
            if($data === null){
                return $this->giaodien($sender);
            }
            $command = "changepetname $data[1] $data[2]";
            $this->getServer()->getCommandMap()->dispatch($sender, $command);
        });
        $form->setTitle("§lĐỔI TÊN THÚ CƯNG");
        $form->addLabel("§7[§e➸§7] §eLƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt");
        $form->addInput("§7-§e Nhập tên pet cũ");
        $form->addInput("§7-§e Nhập tên pet mới");
        $form->sendToPlayer($sender);
        return $form;
   }
    
    public function muapet(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->giaodien($sender);
                return;
            }
            switch($result){
                case 0:
                $this->giaodien($sender);
                break;
                case 1:
                $this->giaodienmuapet($sender);
                break;
                case 2:
                $this->muaphukien($sender);
                break;
            }
        });
        $money = $this->eco->myMoney($sender);
        $coin = $this->coin->myCoin($sender);
        $form->setTitle("§lCỬA HÀNG MUA PET");
        $form->setContent("§7[§e➸§7]§7 Tiền của bạn: §e" . $money.", §7coin của bạn:§e ".$coin);
        $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
        $form->addButton("§l§c•§9 MUA THÚ CƯNG §c•\n§r§8Nhấn để mua",1,"https://cdn-icons-png.flaticon.com/512/1581/1581651.png");
        $form->addButton("§l§c•§9 MUA PHỤ KIỆN THÚ CƯNG §c•\n§r§8Nhấn để mua",1,"https://cdn-icons-png.flaticon.com/512/827/827183.png");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function giaodienmuapet(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if ($result == null){
                $this->muapet($sender);
                return; 
            }
            switch($result){
                case 0:
                $this->muapet($sender);
                break;
                case 1:
                $this->wolf($sender);
                break; 
                case 2:
                $this->pig($sender);
                break; 
                case 3:
                $this->cow($sender);
                break;
                case 4:
                $this->sheep($sender);
                break;
                case 5:
                $this->llama($sender);
                break;
                case 6:
                $this->ocelot($sender);
                break;
                case 7:
                $this->chicken($sender);
                break; 
                case 8:
                $this->slime($sender);
                break; 
                case 9:
                $this->polarbear($sender);
                break; 
                case 10:
                $this->enderdragon($sender);
                break; 
            }
         });
            $coin = $this->coin->myCoin($sender);
            $money = $this->eco->myMoney($sender);
            $form->setTitle("§lMENU MUA THÚ CƯNG");
            $form->setContent("§7[§e➸§7]§7 Tiền của bạn: §e" . $money.", §7Coin của bạn:§e ".$coin);
            $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
            $form->addButton("§c§l• §9MUA THÚ CƯNG CHÓ §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/7298/7298816.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG HEO §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/4388/4388918.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG BÒ §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/375/375062.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG CỪU §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/1656/1656366.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG LẠC ĐÀ §c•\n§r§8ＧIÁ: 300 coin", 1, "https://cdn-icons-png.flaticon.com/512/6420/6420767.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG MÈO §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/3209/3209928.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG GÀ §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/1864/1864499.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG SLIME §c•\n§r§8ＧIÁ: 200 coin", 1, "https://cdn-icons-png.flaticon.com/512/5484/5484595.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG GẤU TUYẾT §c•\n§r§8ＧIÁ: 500 coin", 1, "https://cdn-icons-png.flaticon.com/512/902/902499.png");
            $form->addButton("§c§l• §9MUA THÚ CƯNG RỒNG §c•\n§r§8ＧIÁ: 1000 coin", 1, "https://cdn-icons-png.flaticon.com/512/2602/2602866.png");
            $form->sendToPlayer($sender);
            return $form;
    }
    
    ///chó
    public function wolf(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet wolf {$data[0]} 0.5 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET WOLF]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG CHÓ");
        $form->addInput("\n§7[§e➸§7] §eLƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///heo
    public function pig(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet pig {$data[0]} 0.5 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET PIG]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG HEO");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///bò
    public function cow(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet cow {$data[0]} 0.5 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET COW]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG BÒ");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///cừu
    public function sheep(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet sheep {$data[0]} 0.5 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET SHEEP]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG CỪU");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///lạc đà
    public function llama(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 300;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet llama {$data[0]} 0.5 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET LLAMA]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG LẠC ĐÀ ");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///mèo
    public function ocelot(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet ocelot {$data[0]} 0.6 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET OCELOT]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG MÈO");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///gà
    public function chicken(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet chicken {$data[0]} 0.6 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET CHICKEN]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG GÀ");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///slime
    public function slime(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 200;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet slime {$data[0]} 0.7 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET SLIME]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG SLIME");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///gấu tuyết
    public function polarbear(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 500;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet polarbear {$data[0]} 1 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET POLARBEAR]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG GẤU TUYẾT");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    ///rồng
    public function enderdragon(Player $sender){
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $sender, $data){
            if($data === null) {
                return $this->giaodienmuapet($sender);
            }
            $coin = $this->coin->myCoin($sender);
            $cost = 1000;
            if($coin >= $cost){
                $this->coin->reduceCoin($sender, $cost);    
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "spawnpet enderdragon {$data[0]} 0.4 baby ".$sender->getName());
                        $this->getServer()->broadcastMessage("§7[§a!§7] §aNgười chơi {$sender->getName()} §ađã mua §f[PET ENDERDRAGON]");
                        $this->chapnhan($sender);
                        return true;
                        }else{
                     $this->huybo($sender);
                     }
        });
        $form->setTitle("§lNHẬP TÊN CHO THÚ CƯNG RỒNG");
        $form->addInput("\n§7[§e➸§7] LƯU Ý: §7Không được ghi tên có dấu cách hoặc ký tự đặc biệt, nếu cố ý làm điều đó thì bạn sẽ mất coins mà không nhận được thú cưng\n\n§7-§e Nhập tên thú cưng:\n");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function chapnhan(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->muapet($sender);
                return;
            }
            switch($result){
                case 0:
                $this->muapet($sender);
                break;
            }
        });
        $form->setTitle("§lKẾT QUẢ");
        $form->setContent("§7[§a!§7] Bạn đã mua pet thành công! Hãy kiểm tra.");
        $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function huybo(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->muapet($sender);
                return;
            }
            switch($result){
                case 0:
                $this->muapet($sender);
                break;
            }
        });
        $form->setTitle("§lKẾT QUẢ");
        $form->setContent("§7[§c!§7] Bạn không đủ Coin để mua pet! Hãy kiếm thêm từ việc mine và đổi money sang coin, hoặc nạp :<");
        $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function muaphukien(Player $sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->muapet($sender);
                return;
            }
            switch($result){
                case 0:
                $this->muapet($sender);
                break;
                case 1:
                $this->muayencuoi($sender);
                break;
            }
        });
          $coin = $this->coin->myCoin($sender);
          $money = $this->eco->myMoney($sender);
          $form->setTitle("§lMUA PHỤ KIỆN THÚ CƯNG");
          $form->setContent("§7[§e➸§7]§7 Tiền của bạn: §e" . $money.", §7Coin của bạn:§e ".$coin);
          $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
          $form->addButton("§l§c•§9 MUA YÊN CƯỠI §c•\n§r§8ＧIÁ: 2000000 MONEY",0,"textures/other/yencuoi");
          $form->sendToPlayer($sender);
        return $form;
        }
    
    public function muayencuoi($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createModalForm(function (Player $sender, $data){
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                case 1:
            $money = EconomyAPI::getInstance()->myMoney($sender);
            $cost = 2000000;
            if($money >= $cost){
            EconomyAPI::getInstance()->reduceMoney($sender, $cost);
                $inv = $sender->getInventory();
                $item = ItemFactory::getInstance()->get(329, 0, 1);
                $item->setCustomName("§r§l§aYÊN CƯỠI THÚ CƯNG §r§7- (§aPET§7)");
                $item->setLore(array("\n§r§l§aCÁCH DÙNG YÊN CƯỠI THÚ CƯNG:§r§7 Cầm yên cưỡi sau đó nhấn vào pet để cưỡi"));
                $inv->addItem($item);
                $this->okeform($sender);       
              return true;
            }else{
                $this->badform($sender);
            }
                break;
                case 2:
                $this->muaphukien($sender);
                break;
            }
        });
        $form->setTitle("§lMUA YÊN CƯỠI");
        $form->setContent("§e-§7 Bạn có muốn mua yên cưỡi giá 20000000 Money?"); 
        $form->setButton1("§l§8[§a✔§8] MUA NGAY", 1);
        $form->setButton2("§l§8[§c✘§8] KHÔNG MUA", 2);
        $form->sendToPlayer($sender);
        }
    
    public function okeform($sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->muaphukien($sender);
                return;
            }
            switch($result){
                case 0:
                $this->muaphukien($sender);
                break;
            }
        });
          $form->setTitle("§lKẾT QUẢ");
          $form->setContent("§7[§a!§7] Bạn đã mua thành công 1 yên cưỡi!");
          $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
          $form->sendToPlayer($sender);
        }
    
    public function badform($sender){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
            $result = $data;
            if($result === null){
                $this->muaphukien($sender);
                return;
            }
            switch($result){
                case 0:
                $this->muaphukien($sender);
                break;
            }
        });
          $form->setTitle("§lKẾT QUẢ");
          $form->setContent("§7[§c!§7] Bạn không đủ tiền để mua yên cưỡi!");
          $form->addButton("§l§8[§a⏎§8] QUAY LẠI");
          $form->sendToPlayer($sender);
        }
 }
