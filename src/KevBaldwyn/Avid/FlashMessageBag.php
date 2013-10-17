<?php namespace KevBaldwyn\Avid; 

use Illuminate\Support\MessageBag;
use Illuminate\Session\SessionManager;

class FlashMessageBag extends MessageBag {

    protected $sessionKey = 'flash_messages';
    protected $session;


    public function __construct(SessionManager $session, $messages = array()) {
        $this->session = $session;

        if ($session->has($this->sessionKey)) {
            $messages = array_merge_recursive(
                $session->get($this->sessionKey),
                $messages
            );
        }

        parent::__construct($messages);
    }


    public function flash() {
        $this->session->flash($this->sessionKey, $this->messages);

        return $this;
    }
    
}