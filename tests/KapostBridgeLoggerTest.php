<?php
class KapostBridgeLoggerTest extends FunctionalTest
{
    const USER_AGENT='Kapost XMLRPC::Client';
    
    protected static $fixture_file='KapostBridgeLoggerTest.yml';
    
    
    /**
     * Test to see that the request/response was actaully logged
     */
    public function testBridgeLogging()
    {
        $response=$this->call_service('get-post');
        
        //Ensure we had a 200 response
        $this->assertEquals(200, $response->getStatusCode());
        
        
        //Find the log
        $log=KapostBridgeLog::get()->sort('ID DESC')->first();
        
        
        //Ensure it exists
        $this->assertNotEmpty($log);
        $this->assertNotEquals(false, $log);
        $this->assertTrue($log->exists());
        
        
        //Verify the log has the request details
        $this->assertNotEmpty($log->Request);
        
        
        //Verify the log has the response details
        $this->assertNotEmpty($log->Response);
        
        
        //Verify the called method is correct
        $this->assertEquals('metaWeblog.getPost', $log->Method);
    }
    
    
    /**
     * Tests to verify the password was stripped from the request
     */
    public function testPasswordStripping()
    {
        $response=$this->call_service('get-post');
        
        //Ensure we had a 200 response
        $this->assertEquals(200, $response->getStatusCode());
        
        
        //Find the log
        $log=KapostBridgeLog::get()->sort('ID DESC')->first();
        
        
        //Ensure it exists
        $this->assertNotEmpty($log);
        $this->assertNotEquals(false, $log);
        $this->assertTrue($log->exists());
        
        
        //Parse the XML
        $xml=simplexml_load_string($log->Request);
        
        $this->assertEquals('['._t('LoggedKapostService.PASSWORD_FILTERED', '_PASSWORD FILTERED').']', $xml->params->param[2]->value->string);
    }
    
    /**
     * Tests to verify that the bits of the asset were stripped from the request
     */
    public function testBitsStripping()
    {
        $response=$this->call_service('new-media-asset');
        
        //Ensure we had a 200 response
        $this->assertEquals(200, $response->getStatusCode());
        
        
        //Find the log
        $log=KapostBridgeLog::get()->sort('ID DESC')->first();
        
        
        //Ensure it exists
        $this->assertNotEmpty($log);
        $this->assertNotEquals(false, $log);
        $this->assertTrue($log->exists());
        
        
        //Parse the XML
        $xml=simplexml_load_string($log->Request);
        
        $this->assertEquals('['._t('LoggedKapostService.BITS_FILTERED', '_BASE64 BITS FILTERED').']', $xml->params->param[3]->value->struct->member[2]->value->base64);
    }
    
    /**
     * Calls the api and returns the response
     * @param {string} $mockRequest Mock Request to load
     * @return {SS_HTTPResponse} Response Object
     */
    protected function call_service($mockRequest)
    {
        return $this->post('kapost-service', array(), array('User-Agent'=>self::USER_AGENT), null, file_get_contents(dirname(__FILE__).'/mock_requests/'.$mockRequest.'.xml'));
    }
    
    /**
     * Parses the response from the api
     * @param {string} $body XML Response
     * @return {xmlrpcresp} XML RPC Response Object
     */
    final protected function parseRPCResponse($body)
    {
        $xmlmsg=new xmlrpcmsg('');
        
        return $xmlmsg->parseResponse($body, true, 'phpvals');
    }
}
