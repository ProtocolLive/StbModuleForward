<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/StbModuleForward

namespace ProtocolLive\StbModules;
use ProtocolLive\SimpleTelegramBot\StbInterfaces\StbModuleInterface;
use ProtocolLive\SimpleTelegramBot\StbObjects\{
  StbAdminModules,
  StbDatabase,
  StbModuleHelper,
};
use ProtocolLive\TelegramBotLibrary\TgInterfaces\TgForwadableInterface;
use ProtocolLive\TelegramBotLibrary\TgObjects\TgCallback;
use ProtocolLive\TelegramBotLibrary\TgObjects\TgObject;

/**
 * @version 2023.05.29.00
 */
class Forward
extends StbModuleHelper
implements StbModuleInterface{
  public static function Command():void{}

  public static function Install():void{
    /**
     * @var TgCallback $Webhook
     */
    global $Db, $Webhook, $Bot;
    DebugTrace();
    parent::InstallHelper(
      __CLASS__,
      Commit: false
    );
    if($Db->ListenerAdd(TgObject::class, __CLASS__) === false):
      parent::MsgError();
      return;
    endif;
    $Bot->CallbackAnswer(
      $Webhook->Id,
      '✅ Instalação concluída'
    );
    parent::InstallHelper2();
  }

  public static function Listener():void{
    global $Webhook, $Bot;
    if($Webhook instanceof TgForwadableInterface === false):
      return;
    endif;
    $Bot->MessageForward(
      $Webhook->Data->Chat->Id,
      $Webhook->Data->Id,
      Admin
    );
  }

  public static function UnInstall():void{
    /**
     * @var TgCallback $Webhook
     */
    global $Webhook, $Bot, $Db;
    DebugTrace();
    $Db->ModuleUninstall(__CLASS__);
    $Bot->CallbackAnswer(
      $Webhook->Id,
      '✅ Desinstalação concluída'
    );
    StbAdminModules::Callback_Modules();
  }
}