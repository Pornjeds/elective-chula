<?php

class HttpBasicUserAuth extends \Slim\Middleware
{
    /**
     * @var string
     */
    protected $realm;
 
    /**
     * Constructor
     *
     * @param   string  $realm      The HTTP Authentication realm
     */
    public function __construct($realm = 'User Protected Area')
    {
        $this->realm = $realm;
    }
 
    /**
     * Deny Access
     *
     */   
    public function deny_access() {
        $res = $this->app->response();
        $res->status(401);
        //$res->header('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));        
    }

    /**
     * Call
     *
     * This method will check the SESSION for previous authentication. If
     * the request has already authenticated, the next middleware is called. Otherwise,
     * a 401 Authentication Required response is returned to the client.
     */
    public function call()
    {
        $req = $this->app->request();
        $res = $this->app->response();

        //loginStatus will be set after admin successfully login (./admin_login.html -> ./server/admin_login.php)
        if (isset($_SESSION['loginStatus']) && isset($_SESSION['loginType']) && $_SESSION['loginStatus'] == 1 && $_SESSION['loginType'] == 'user'){
            $this->next->call();
        } else {
            $this->deny_access();
        }
    }
}