<?php
namespace IComeFromTheNet\BookMe\Tests;

use IComeFromTheNet\BookMe\BookMeService;
use Doctrine\DBAL\DBALException;

class RulesPackageTest extends BasicTest
{
    
    
    public function testMinuteParserGoodFormats()
    {
        $db = $this->getDoctrineConnection();
        
        # Test if valid formats create expected result set in 
        # the result tmp table;
        
        # Test for the default '*'
        $db->executeQuery("CALL bm_rules_parse('*','minute')");
        
        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        
        $this->assertEquals($result['range_open'],"1");
        $this->assertEquals($result['range_closed'],"59");
        $this->assertEquals($result['value_type'],"minute");
        $this->assertEquals($result['mod_value'],1);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ## e.g scalar value range 1 to 59
        $db->executeQuery("CALL bm_rules_parse('56','minute')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"56");
        $this->assertEquals($result['range_closed'],"56");
        $this->assertEquals($result['value_type'],"minute");
        $this->assertEquals($result['mod_value'],1);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ##-## e.g range scalar values
        $db->executeQuery("CALL bm_rules_parse('34-59','minute')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"34");
        $this->assertEquals($result['range_closed'],"59");
        $this->assertEquals($result['value_type'],"minute");
        $this->assertEquals($result['mod_value'],1);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ##-## e.g range scalar values
        $db->executeQuery("CALL bm_rules_parse('*/20','minute')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"1");
        $this->assertEquals($result['range_closed'],"59");
        $this->assertEquals($result['value_type'],"minute");
        $this->assertEquals($result['mod_value'],20);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ##/## e.g 6/3 short for 6-59/3
        $db->executeQuery("CALL bm_rules_parse('6/3','minute')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"6");
        $this->assertEquals($result['range_closed'],"59");
        $this->assertEquals($result['value_type'],"minute");
        $this->assertEquals($result['mod_value'],3);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
     
        
    }
    
    
    public function testMinuteParseFailures()
    {
        $db = $this->getDoctrineConnection();
        $patterns = array(
            'one'    => '60'
            ,'two'   => 'a'
            ,'three' => '-1'
            ,'four'  =>'60-59'
            ,'five' => '6-60'
            ,'six' => '**/20'
            ,'seven' => '60/3'
            ,'eight'   => '6/*'
            ,'nine'  => '6-60/3'
            ,'ten' => '6-*/3'
            ,'eleven' => '-1-59/3'
            
            
        );
        
        
        foreach($patterns as $key => $pattern) {
        
            try {
                $db->executeQuery("CALL bm_rules_parse('?','minute')",array($pattern));
                
                $this->assertTrue(false,'Test for minute parse fails has failed to cause an exception');
            }
            catch(DBALException $e) {
                $this->assertContains('1644 not support cron minute format',$e->getMessage());
            }
            
        }
    
        
    }


    public function testHourValidCombinations() 
    {
        $db = $this->getDoctrineConnection();
        
        # Test if valid formats create expected result set in 
        # the result tmp table;
        
        # Test for the default '*'
        $db->executeQuery("CALL bm_rules_parse('*','hour')");
        
        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        
        $this->assertEquals($result['range_open'],"0");
        $this->assertEquals($result['range_closed'],"23");
        $this->assertEquals($result['value_type'],"hour");
        $this->assertEquals($result['mod_value'],1);    
        
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ## e.g scalar value range 0 to 23
        $db->executeQuery("CALL bm_rules_parse('23','hour')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"23");
        $this->assertEquals($result['range_closed'],"23");
        $this->assertEquals($result['value_type'],"hour");
        $this->assertEquals($result['mod_value'],1);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ##-## e.g range scalar values
        $db->executeQuery("CALL bm_rules_parse('5-9','hour')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"5");
        $this->assertEquals($result['range_closed'],"9");
        $this->assertEquals($result['value_type'],"hour");
        $this->assertEquals($result['mod_value'],1);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ##-## e.g range scalar values
        $db->executeQuery("CALL bm_rules_parse('*/20','hour')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        
        $this->assertEquals($result['range_open'],"0");
        $this->assertEquals($result['range_closed'],"23");
        $this->assertEquals($result['value_type'],"hour");
        $this->assertEquals($result['mod_value'],20);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        # Test format ##/## e.g 6/3 short for 6-23/3
        $db->executeQuery("CALL bm_rules_parse('6/3','hour')");

        $result = $db->fetchAssoc('SELECT * FROM bm_parsed_ranges');
        $this->assertEquals($result['range_open'],"6");
        $this->assertEquals($result['range_closed'],"23");
        $this->assertEquals($result['value_type'],"hour");
        $this->assertEquals($result['mod_value'],3);
        
        $db->executeQuery("TRUNCATE bm_parsed_ranges");
        
        
        
    }
    
    
    
    
    
}
/* End of Class */