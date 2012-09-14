<?php
echo 'hello1';

print ('dobryi dzen');
require_once ('config.php');
require_once ('helper.php');
require_once ('parser.php');
require_once ('movies.php');
require_once ('sql.php');
require_once ('PHPUnit/Autoload.php');

class AppTest extends PHPUnit_Framework_TestCase{
    
    public $date = "2012-01-20";
    
    public function __constructor($name){
        parent::__constructor($name);
    }
    
    function testParser(){
        global $config;
        set_time_limit($config['time_limit']);
        $parser = new Parser;
        $parser->date = $this->date;
        $page_info = $parser->getPageInfo($config['url']);
        
        $this->assertArrayHasKey("content", $page_info, "CAN'T FIND 'CONTENT' KEY IN RESULT ARRAY.");
        $this->assertEquals($page_info['errorno'], 0, "ERROR OCCURS DURING PAGE LOADING.");
        
        $parser->page = $page_info['content'];
        $html = $parser->parsePage();
        $this->assertGreaterThanOrEqual(10, count($html), "CAN'T FIND 10 MOVIES ON THE GIVEN PAGE.");
        
        $this->assertArrayHasKey("rank", $html[1], "CAN'T FIND 'RANK' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $html[1]['rank'], "CAN'T FIND RANK ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("title", $html[1], "CAN'T FIND 'TITLE' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $html[1]['title'], "CAN'T FIND TITLE ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("rate", $html[1], "CAN'T FIND 'RATE' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $html[1]['rate'], "CAN'T FIND RATE ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("year", $html[1], "CAN'T FIND 'YEAR' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $html[1]['year'], "CAN'T FIND YEAR ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("voters", $html[1], "CAN'T FIND 'VOTERS' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $html[1]['voters'], "CAN'T FIND VOTERS ON THE GIVEN PAGE.");
        
    }
    
    function testFrontend(){
        global $sql;
        global $config;
        
        $sql = new sqlme($config['db_host'], $config['db_login'], $config['db_password'], $config['db_name']);
        $movies = new Movies;
        $list = $movies->getListByDate($this->date);
        
        $this->assertGreaterThanOrEqual(10, count($list), "CAN'T FIND 10 MOVIES ON THE GIVEN PAGE.");
        
        $this->assertArrayHasKey("rank", $list[1], "CAN'T FIND 'RANK' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $list[1]['rank'], "CAN'T FIND RANK ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("title", $list[1], "CAN'T FIND 'TITLE' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $list[1]['title'], "CAN'T FIND TITLE ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("rate", $list[1], "CAN'T FIND 'RATE' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $list[1]['rate'], "CAN'T FIND RATE ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("year", $list[1], "CAN'T FIND 'YEAR' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $list[1]['year'], "CAN'T FIND YEAR ON THE GIVEN PAGE.");
        $this->assertArrayHasKey("voters", $list[1], "CAN'T FIND 'VOTERS' KEY IN RESULT ARRAY.");
        $this->assertNotSame(false, $list[1]['voters'], "CAN'T FIND VOTERS ON THE GIVEN PAGE.");
    }
    
    function testDateFormatChecking(){
        $this->assertTrue(Helper::checkDateFormat("2012-01-20", false), "DATE FORMAT CHECKING ERROR #1");
        $this->assertFalse(Helper::checkDateFormat("any string", false), "DATE FORMAT CHECKING ERROR #2");
        $this->assertFalse(Helper::checkDateFormat("2012-13-20", false), "DATE FORMAT CHECKING ERROR #3");
        $this->assertFalse(Helper::checkDateFormat("2012-01-32", false), "DATE FORMAT CHECKING ERROR #4");
    }
    
    function testDbConnection(){
        global $sql;
        global $config;
        
        $sql = new sqlme($config['db_host'], $config['db_login'], $config['db_password'], $config['db_name']);
        
        $this->assertNotSame(false, $sql->cid, "DATABASE CONNECTION ERROR");
        $this->assertNotSame(false, $sql->db, "DATABASE SELECTION ERROR");
    }
}

$suite = new PHPUnit_Framework_TestSuite();
$suite->addTest(new AppTest('testParser'));
$suite->addTest(new AppTest('testFrontend'));
$suite->addTest(new AppTest('testDateFormatChecking'));
$suite->addTest(new AppTest('testDbConnection'));

PHPUnit_TextUI_TestRunner::run($suite);

?>