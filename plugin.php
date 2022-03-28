<?php

// Das Plugin exportiert den gesamten Inhalt in das Verzeichnis EXPORT
// als MD-File für den Einsatz im PICO CMS. Empfehlenswert ist zudem die
// Erhöhung des Wertes in:
//
//     bl-kernel/boot/variables.php
//
// auf
//
//     define('NOTIFICATIONS_AMOUNT', 120);
//
// 120 statt nur 10, damit die Arbeit des Plugins im Dashboard besser
// geprüft werden kann.

class pluginBludit2Pico extends Plugin {

	public function init()
	{
		$this->formButtons = false;
	}

	public function post()
	{
		if (isset($_POST['createBackup'])) {
			return $this->createPages();
			return $this->createIndex();
		}
		return false;
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';
		$html .= '<button name="createBackup" value="true" class="btn btn-primary" type="submit">Export</button>';

		return $html;
	}

	public function createPages() {
		global $pages;
		global $syslog;
		global $L;

		$pageNumber    	= 1;
    	$numberOfItems 	= -1;
    	$onlyPublished 	= true;
    	$items         	= $pages->getList($pageNumber, $numberOfItems, $onlyPublished);

    	foreach ($items as $key) {
			$page = buildPage($key);

			$content = '---'										."\n";
			$content .= 'Title: '		.$page->title()				."\n";
			$content .= 'Author: '		.$page->user('nickname')	."\n";
			$content .= 'Date: '		.$page->date()				."\n";
			$content .= 'Robots: '		.'noindex,nofollow'			."\n";
			$content .= 'Template: '	.'index'					."\n";
			$content .= 'logo: '		.$page->coverimage()		."\n";
			$content .= 'Featured: '	.'true'						."\n";
			$content .= 'Description: '	.$page->description()		."\n";
			$content .= '---'										."\n";
			$content .= $page->content();

			$category = str_replace(' ','-',$page->category());
			$category = str_replace('.','-',$category);
			$category = strtolower($category);

			if (!mkdir('export/'.$category, 0777, true)) {
				//$syslog->add(array('dictionaryKey'=>'Ordner','notes'=>$category.' nicht erstellt' ));
			}
			
			file_put_contents('export/'.$category.'/'.$page->slug().'.md', $content);
			$syslog->add(array('dictionaryKey'=>'Out','notes'=>$page->slug().'.md' ));
			
		}
	}

	public function createIndex()
	{
		// nothing
	}
}