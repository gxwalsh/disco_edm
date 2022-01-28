<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $client_project;
    public function __construct() {
        $this->clients = new \SplObjectStorage;
	$this->client_project = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
	$qs = $conn->httpRequest->getUri()->getQuery();
	parse_str($qs);
	array_push($this->client_project,array('connection'=>$conn->resourceId,'project'=>$project_id));
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

//trying to only send projects the correct info
	echo json_encode($this->client_project);
	$key = array_search($from->resourceId, array_column($this->client_project,'connection'));
	$sender_project =  $this->client_project[$key]['project'];





        foreach ($this->clients as $client) {
            	
		if ($from !== $client) {
                $receiverkey = array_search($client->resourceId, array_column($this->client_project,'connection'));
				if ($this->client_project[$receiverkey]['project'] == $sender_project) {
				// The sender is not the receiver, send to each client connected
					$client->send($msg) ;
				}
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
     public function test() {

	var_dump(get_object_vars($this));
	}

}
