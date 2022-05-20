<?php 

//
// INTERFACES START
//

interface SectorFactoryInterface {
    public static function createSector($sectorName);
}

interface DressCodeInterface {
    public function dressCode($msg);
}

interface MeetingsInterface {
    public function scheduleMeeting($date, $time, $client);
}

interface WorkHoursInterface {
    public function workHours($from, $to);
}

interface ProductFactoryInterface {
    public static function createProduct($productName);
}

//
// INTERFACES END
//

//
// CLASSES START
//

class Company {
    private static $instance;
    private $sectors = [];

    private function __construct(){}

    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new Company();
        }

        return self::$instance;
    }

    public function addSectors($sector, $num) {
        for ($i=0; $i < $num; $i++) { 
            $this->sectors[] = $sector;
        }
    }

    public function getSectors() {
        return $this->sectors;
    }

    public function findSector($sectorToFind) {
        foreach ($this->sectors as $sector) {
            if($sector->getSectorName() == $sectorToFind) {
                return $sector;
            }
        }

        return;
    }
}

abstract class Sector {
    protected $sectorName;
    protected $employees = [];
    protected $productsLager = [];

    public function __construct($sectorName) {
        $this->sectorName = $sectorName;
    }

    public function getSectorName() {
        return $this->sectorName;
    }

    public function addEmployee($employee, $num) {
        for ($i=0; $i < $num; $i++) { 
            $this->employees[] = $employee;
       }
    }

    public function addProductToLager($product, $quantity) {
       for ($i=0; $i < $quantity; $i++) { 
            $this->productsLager[] = ProductFactory::createProduct($product);
       }
    }
}

class Employee {
    public function __construct(){}
}

class Buyer {
    private $buyerName;
    public $buyerProducts = [];

    public function __construct($buyerName){
        $this->buyerName = $buyerName;
    }

    public function getBuyerName() {
        return $this->buyerName;
    }

    public function buyProduct($product, $marketPlace) {  
        $marketPlace->sellProduct($product);
        return $product;
    }
}

abstract class Product {
    protected $productName;

    public function __construct($productName) {
        $this->productName = $productName;
    }

    public function getProductName() {
        return $this->productName;
    }
}

class Monitor extends Product {}

class Keyboard extends Product {}

class Laptop extends Product {}

// Factory Pattern
class ProductFactory implements ProductFactoryInterface {
    public static function createProduct($productName)
    {
        if($productName == 'monitor') {
            return new Monitor($productName);
        } if($productName == 'keyboard') {
            return new Keyboard($productName);
        } if($productName == 'laptop') {
            return new Laptop($productName);
        }
    }
}

class Factory extends Sector implements WorkHoursInterface {
    private $workHours;

    public function workHours($from, $to)
    {
        $this->workHours = "Radno vreme je od $from do $to <br/>";
    }

    public function getWorkHours() {
        return $this->workHours;
    }
}

class MarketPlace extends Sector implements DressCodeInterface, WorkHoursInterface {
    private $workHours;

    public function workHours($from, $to)
    {
        $this->workHours = "Radno vreme je od $from do $to <br/>";
    }

    public function dressCode($msg)
    {
        echo $msg;
    }

    public function getWorkHours() {
        return $this->workHours;
    }

    public function sellProduct($product) { 
        foreach ($this->productsLager as $prodId => $prod) {
            if($prod->getProductName() == $product) {
                echo "Kupac je kupio proizvod {$prod->getProductName()}";
                break;
            }
        }
    }
}

class ProcurementDep extends Sector implements MeetingsInterface {
    private $meetings = [];

    public function scheduleMeeting($date, $time, $client)
    {
        $this->meetings[] = "Sastanak zakazan $date u $time za klijenta $client <br/>";
    }

    public function getMeetings() {
        return $this->meetings;
    }
}

class MarketingDep extends Sector implements DressCodeInterface, MeetingsInterface {
    private $dressCode;
    private $meetings = [];

    public function dressCode($msg)
    {
        $this->dressCode = $msg;
    }
    public function scheduleMeeting($date, $time, $client)
    {
        $this->meetings[] = "Sastanak zakazan $date u $time za klijenta $client <br/>";
    }
}

class SectorFactoryMethod implements SectorFactoryInterface {
    public static function createSector($sectorName)
    {
        if($sectorName == 'factory') {
            return new Factory($sectorName);
        } else if($sectorName == 'marketPlace') {
            return new MarketPlace($sectorName);
        } else if($sectorName == 'procurementDep') {
            return new ProcurementDep($sectorName);
        } else if($sectorName == 'marketingDep') {
            return new MarketingDep($sectorName);
        }
    }
}

//
// CLASSES END
//


//
// SIMULACIJA
//

$benComputersCompany = Company::getInstance();

$benComputersCompany->addSectors(SectorFactoryMethod::createSector('factory'), 4);
$benComputersCompany->addSectors(SectorFactoryMethod::createSector('marketPlace'), 6);
$benComputersCompany->addSectors(SectorFactoryMethod::createSector('procurementDep'), 1);
$benComputersCompany->addSectors(SectorFactoryMethod::createSector('marketingDep'), 2);

$benComputersCompany->findSector('marketingDep')->dressCode('Poslovno odelo, kravata nije obavzna');
$benComputersCompany->findSector('procurementDep')->scheduleMeeting('20.10.2022', '08:00', 'Mile Teslic');

$benComputersCompany->findSector('marketPlace')->addProductToLager('keyboard', 4);
$benComputersCompany->findSector('marketPlace')->addProductToLager('monitor', 3);
$benComputersCompany->findSector('marketPlace')->addProductToLager('laptop', 5);

$benComputersCompany->findSector('factory')->addProductToLager('keyboard', 5);
$benComputersCompany->findSector('factory')->addProductToLager('monitor', 7);
$benComputersCompany->findSector('factory')->addProductToLager('laptop', 6);

$benComputersCompany->findSector('marketingDep')->addEmployee(Employee::class, 2);

$buyer = new Buyer('Danilo');

$buyer->buyProduct('keyboard', $benComputersCompany->findSector('marketPlace'));



echo "<pre>";
echo "</pre>";



