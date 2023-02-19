<?php

namespace Core\Controller;
use Core\Base\Controller;
use Core\Helpers\Helper;
use Core\Model\User;
use Core\Model\Subject;
use Core\Model\Chat;

class Chats extends Controller
{ 
    public function send_message() {
        $chats= new Chat;
        $chats->create($_POST);
      
    }
  
    public function get_chat_history($user_id) {
      // retrieve the chat history for the user
      $query = "SELECT * FROM chat_messages WHERE sender_id = ? OR receiver_id = ? ORDER BY timestamp DESC";
      $stmt = $this->db->prepare($query);
      $stmt->bind_param("ii", $user_id, $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $history = $result->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
  
      return $history;
    }
  }
  
  // AJAX request handler
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat = new Chat($db);
    
    if ($_POST['action'] === 'send_message') {
      // validate and sanitize input
      $sender_id = filter_var($_POST['sender_id'], FILTER_SANITIZE_NUMBER_INT);
      $receiver_id = filter_var($_POST['receiver_id'], FILTER_SANITIZE_NUMBER_INT);
      $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
  
      // send the chat message
      $chat->send_message($sender_id, $receiver_id, $message);
    }
  
    if ($_POST['action'] === 'get_chat_history') {
      // validate and sanitize input
      $user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
  
      // retrieve the chat history for the user
      $history = $chat->get_chat_history($user_id);
  
      // send the chat history back to the client as JSON
      header('Content-Type: application/json');
      echo json_encode($history);
    }

} 