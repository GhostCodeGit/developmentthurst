<?php
namespace Ide\Screens;

use FMX\Forms\TForm;
use FMX\StdCtrls\TButton;
use FMX\StdCtrls\TPanel;
use FMX\StdCtrls\TLabel;
use FMX\StdCtrls\TCalloutPanel;
use FMX\Layouts\TLayout;
use FMX\Layouts\TVertScrollBox;
use FMX\Objects\TText;

use FMX\Objects\TImage;
use Vcl\Graphics\TPicture;
use Vcl\Graphics\TBitmap;
use Vcl\Imaging\pngimage\TPngImage;

class FooterPanel {

	private $screen;
	private $messages = [];

	public function updateMessages($panelMessages, $panelMessagesContents) {
		static $rendered = [];
			for($j = 0; $j <= 5; $j++) {
				for($i = 0; $i < $panelMessagesContents->content->componentCount; $i++) {
					$panelMessagesContents->content->components($i)->destroy();
				}
			}
			
			$y = 30;
			$lmessages = [];
			$timeout = 300;
			foreach($this->messages as $key => $message) {
				$layoutMessage = new TLayout;
				$layoutMessage->parent = $panelMessagesContents->content;
				$layoutMessage->position->x = 5;
				$layoutMessage->position->y = $y;
				$layoutMessage->width = $panelMessagesContents->width - ($layoutMessage->position->x * 2);
				$layoutMessage->height = 48;
				$layoutMessage->align = 'alTop';
				if(!in_array(md5($message[1]), $rendered)) {
					$layoutMessage->opacity = 0;
					setTimeout($timeout, function()use($layoutMessage) {
						setInterval(1, function($timer)use($layoutMessage) {
							if((int)$layoutMessage->opacity == 1) {
								$timer->enabled = false;
								return;
							}
							$layoutMessage->opacity += 0.02;
						});
					});
					$timeout += 100;
					$rendered[] = md5($message[1]);
				}
				
				$layoutMessage1 = new TPanel;
				$layoutMessage1->parent = $layoutMessage;
				$layoutMessage = $layoutMessage1;
				$layoutMessage->align = 'alTop';
				$layoutMessage->height = $layoutMessage->parent->height - 5;
				
				$lmessages[] = $layoutMessage;
				$y += $layoutMessage->height + 2;
					
				switch($message[0]) {
					case 0: {
						$icon = $ide->icons->get("error");
						$type = 'Ошибка';
						break;
					}
					case 1: 
					default: {
						$icon = $ide->icons->get("idea");
						$type = 'Информация';
						break;
					}
				}
				
				$image = new TImage;
				$image->parent = $layoutMessage;
				$image->width = 16;
				$image->height = 16;
				$image->position->x = 5;
				$image->position->y = 5;
				$image->bitmap->loadFromFile($icon);
					
				$layoutTypeLabel = new TText($type);
				$layoutTypeLabel->parent = $layoutMessage;
				$layoutTypeLabel->position->x = $image->width + $image->position->x + 5;
				$layoutTypeLabel->position->y = 3;
				$layoutTypeLabel->styledSettings = '';
				$layoutTypeLabel->wordWrap = false;
				$layoutTypeLabel->autoSize = true;
				$layoutTypeLabel->font->size = 14;
				$layoutTypeLabel->width = $layoutMessage->width - ($layoutTypeLabel->position->x * 2);
					
				$layoutDateLabel = new TText($message[3]);
				$layoutDateLabel->parent = $layoutMessage;
				$layoutDateLabel->width = 55;
				$layoutDateLabel->position->x = $layoutMessage->width - $layoutDateLabel->width;
				$layoutDateLabel->position->y = $layoutTypeLabel->position->y;
				$layoutDateLabel->styledSettings = '';
				$layoutDateLabel->wordWrap = false;
				$layoutDateLabel->autoSize = true;
				$layoutDateLabel->font->size = 13;
				$layoutDateLabel->anchors = 'akRight';
					
				$layoutMessageLabel = new TLabel($message[1]);
				$layoutMessageLabel->parent = $layoutMessage;
				$layoutMessageLabel->position->x = $layoutTypeLabel->position->x + 2;
				$layoutMessageLabel->position->y = -5;
				$layoutMessageLabel->wordWrap = false;
				$layoutMessageLabel->width = $layoutMessage->width - ($layoutMessageLabel->position->x * 2);
				$layoutMessageLabel->height = 70;
			}
	}
	
	public function __construct($parent = null) {
		if($parent == null) {
			$screen = new TForm; 
			$screen->position = 'poScreenCenter';
		} else $screen = $parent;
		
		$this->messages[] = [1, "Нет сообщений.", false, date("H:i:s")];
		
		$labelMessage = new TText;
		$labelMessage->parent = $screen;
		$labelMessage->styledSettings = '';
		$labelMessage->font->size = 14;
		$labelMessage->autoSize = true;
		$labelMessage->wordWrap = false;

		$image = new TImage;
		$image->parent = $screen;
		$image->width = 16;
		$image->height = 16;
		
		$imageMore = new TImage;
		$imageMore->parent = $screen;
		$imageMore->width = 16;
		$imageMore->height = 16;
		$imageMore->on("click", function($sender)use(&$panelMessages, &$labelMessages, &$panelMessagesContents) {
			$this->updateMessages($panelMessages, $panelMessagesContents);
			$panelMessages->visible = $panelMessages->visible == "True" ? false : true;
			$labelMessages->bringToFront();
		});
		
		$panelMessages = new TCalloutPanel;
		$panelMessages->parent = $screen->parent;
		$panelMessages->width = (int)($screen->width / 2);
		$panelMessages->height = (int)($screen->parent->height / 2);
		$panelMessages->visible = false;
		$panelMessages->calloutPosition = 'cpBottom';
		$panelMessages->calloutOffset = ($panelMessages->width - $panelMessages->calloutWidth) - 18;
		$panelMessages->anchors = 'akRight, akBottom';
			
			$space = new TLayout;
			$space->parent = $panelMessages;
			$space->width = 5;
			$space->align = 'alLeft';
			$space = new TLayout;
			$space->parent = $panelMessages;
			$space->width = 5;
			$space->align = 'alRight';
			$space = new TLayout;
			$space->parent = $panelMessages;
			$space->height = 5;
			$space->align = 'alBottom';
		
		$panelMessagesContents = new TVertScrollBox;
		$panelMessagesContents->parent = $panelMessages;
		$panelMessagesContents->align = 'alClient';
		$panelMessagesContents->showScrollbars = false;
		
			$space = new TLayout;
			$space->parent = $panelMessages;
			$space->height = 30;
			$space->align = 'alTop';
			
		$labelMessages = new TText;
		$labelMessages->parent = $space;
		$labelMessages->position->x = 5;
		$labelMessages->position->y = 5;
		$labelMessages->styledSettings = '';
		$labelMessages->autoSize = true;
		$labelMessages->wordWrap = false;
		$labelMessages->font->size = 15;
		
		setInterval(100, function()use($labelMessages, $labelMessage, $image, $imageMore, $panelMessages, $panelMessagesContents, $screen) {
			$panelMessages->position->x = ($imageMore->position->x - $panelMessages->width) + 20;
			$panelMessages->position->y = $screen->position->y - $panelMessages->height - 5;
			
			$labelMessage->position->y = ($labelMessage->parent->height - $labelMessage->height) / 2;
			$labelMessage->position->x = $labelMessage->parent->width - $labelMessage->width - 5 - (count($this->messages)!==1?20:0);
			
			$labelMessages->text = "Все уведомления (".count($this->messages).")";
			
			$icon = $ide->icons->get("idea", 16);
			switch(end($this->messages)[0]) {
				case 0: {
					$icon = $ide->icons->get("error");
					break;
				}
				case 1: {
					$icon = $ide->icons->get("idea");
					break;
				}
			}
			$image->bitmap->loadFromFile($icon);
			$image->position->y = ($image->parent->height - $image->height) / 2;
			$image->position->x = $labelMessage->position->x - (16+3);
			
			if(count($this->messages)!==1) {
				$imageMore->bitmap->loadFromFile($ide->icons->get("top"));
				$imageMore->position->y = ($imageMore->parent->height - $imageMore->height + 4) / 2;
				$imageMore->position->x = $labelMessage->position->x + $labelMessage->width;
			} else {
				$imageMore->bitmap->clear();
			}
			
			$message = end($this->messages)[1];
			$labelMessage->text = substr($message, 0, 100).(strlen($message)>=100?"...":"");
			$date = end($this->messages)[3];
			$date = time() - strtotime($date);
			$date = (int)($date / 60);
			if($date == 0)
				$date = "Только что";
			else $date .= " мин. назад";
			$labelMessage->text .= " - ". $date;
		});
		
		$this->screen = $screen;
	}
	
	public function addMessage($message, $type, $try_again) {
		$this->messages[] = [$type, $message, $try_again, date("H:i:s"), false];
	}
	
	public function showGo() {
		$screen = $this->screen;
		
		if($screen->className == "FMX.Forms.TForm") $screen->show();
	}
	
	public function destroy() {
		
	}
	
}
?>