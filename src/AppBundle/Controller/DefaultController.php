<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Contact;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/telegram/", name="telegram")
     */
    public function telegramAction(Request $request)
    {

        /** @var BotApi $api */
        $api = $this->get('bo_shurik_telegram_bot.api');
        $result = $api->getUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
        $result = array_pop($result);

//        dump($result->getMessage()->getContact());
//        die();

        $text = $result->getMessage()->getText(); //Текст сообщения
        $chat_id = $result->getMessage()->getChat()->getId(); //Уникальный идентификатор пользователя
        $name =  $result->getMessage()->getFrom()->getUsername(); //Юзернейм пользователя
        $keyboard = [
//            ["Последние статьи"],
//            ["Картинка"],
//            ["Гифка"],
            [
                ["text"=> "My phone number", "request_contact" => true, "request_location" => true]
            ]
        ]; //Клавиатура

        if($result->getMessage()->getContact() instanceof Contact){
            $contact = $result->getMessage()->getContact();
            $user = new User();
            $user->setPhone($contact->getPhoneNumber());
            $user->setUsername($contact->getFirstName());
            $user->setEmail($contact->getUserId()."sdsds@sdsd.com");
            $user->setTelegramId($contact->getUserId());
            $user->setPassword("qqqqqqq");
            $user->setRoles(["ROLE_USER"]);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        }

        if($text){
            if ($text == "/start") {
                $reply = "Отправьте вашы данные";
                $reply_markup = new  ReplyKeyboardMarkup($keyboard);
//                $api->sendMessage($chat_id, $reply, null, false, null, $reply_markup);
                $api->sendMessage($chat_id, $reply, "Markdown", false, null, $reply_markup);
            }
//            elseif ($text == "/help") {
//                $reply = "Информация с помощью.";
//                $api->sendMessage($chat_id, $reply);
//            }elseif ($text == "Картинка") {
//                $url = "https://68.media.tumblr.com/6d830b4f2c455f9cb6cd4ebe5011d2b8/tumblr_oj49kevkUz1v4bb1no1_500.jpg";
//                $api->sendPhoto([ 'chat_id' => $chat_id, 'photo' => $url, 'caption' => "Описание." ]);
//            }elseif ($text == "Гифка") {
//                $url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
//                $api->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Описание." ]);
//            }elseif ($text == "Последние статьи") {
//                $html=simplexml_load_file('http://netology.ru/blog/rss.xml');
//                foreach ($html->channel->item as $item) {
//                    $reply .= "\xE2\x9E\xA1 ".$item->title." (<a href='".$item->link."'>читать</a>)\n";
//                }
//                $api->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
//            }else{
//                $reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
//                $api->sendMessage($chat_id, $reply, 'HTML');
////                $api->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
//            }
        }else{
            $api->sendMessage($chat_id,"Отправьте текстовое сообщение." );
        }
        die();

        // replace this example code with whatever you need
        return [];
    }
}
