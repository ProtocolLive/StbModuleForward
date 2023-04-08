<?php
//Protocol Corporation Ltda.
//https://github.com/SantuarioMisericordiaRJ/StbModuleDiarioSantaFaustina
//2023.04.06.00

use ProtocolLive\SimpleTelegramBot\StbObjects\{
  StbAdminModules,
  StbDatabase,
  StbDbListeners,
  StbModuleHelper,
  StbModuleInterface
};
use ProtocolLive\TelegramBotLibrary\TelegramBotLibrary;
use ProtocolLive\TelegramBotLibrary\TgObjects\{
  TgCallback,
  TgPhoto,
  TgVideo
};

class Forward
extends StbModuleHelper
implements StbModuleInterface{
  /**
   * @global StbDatabase $Db
   * @global TelegramBotLibrary $Bot
   */

  private static function Encaminhar(
    int $Chat,
    int $Id
  ){
    global $Bot;
    $Bot->MessageForward(Admin, $Chat, $Id);
  }

  public static function Install():void{
    /**
     * @var TgCallback $Webhook
     */
    global $Db, $Webhook, $Bot;
    DebugTrace();
    $pdo = $Db->GetCustom();

    parent::InstallHelper(
      $pdo,
      [],
      false
    );
    if($Db->ListenerAdd(StbDbListeners::Photo, __CLASS__) === false):
      parent::MsgError($pdo);
      return;
    endif;
    if($Db->ListenerAdd(StbDbListeners::Video, __CLASS__) === false):
      parent::MsgError($pdo);
      return;
    endif;
  
    $Bot->CallbackAnswer(
      $Webhook->Id,
      '✅ Instalação concluída'
    );
    parent::InstallHelper2($pdo);
  }

  public static function Listener_Photo(){
    /**
     * @var TgPhoto $Webhook
     */
    global $Webhook;
    self::Encaminhar(
      $Webhook->Data->Chat->Id,
      $Webhook->Data->Id
    );
  }

  public static function Listener_Video(){
    /**
     * @var TgVideo $Webhook
     */
    self::Encaminhar(
      $Webhook->Data->Chat->Id,
      $Webhook->Data->Id
    );
  }

  public static function UnInstall():void{
    /**
     * @var TgCallback $Webhook
     */
    global $Db, $Webhook, $Bot;
    DebugTrace();
    $pdo = $Db->GetCustom();
    $pdo->exec('drop table ' . parent::ModTable('inscritos'));
    parent::UninstallHelper(
      $pdo,
      []
    );
    $Bot->CallbackAnswer(
      $Webhook->Id,
      '✅ Desinstalação concluída'
    );
    StbAdminModules::Callback_Modules();
  }
}