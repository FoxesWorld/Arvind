<?php 
	if(defined("INCLUDE_CHECK")){
		die(getyText());
	} else {
		die("Hacking Attempt!");
	}

	function getyText(){
			$TextArray = array(
			"Куриная основа!",
			"Секретный план Нотча!",
			"Не без Эксепшнов!",
			"Придумано Лисами!",
			"Основано на реальных событиях!",
			"Нужно больше золота!",
			"AidenFox & DarkFox!",
			"Расскажи своей маме!",
			"А ты хорош!",
			"K4dj1t опрятен!",
			"Может содержать орехи!",
			"Лучше, чем добыча!",
			"Автообновление!",
			"Теперь больше полигонов!",
			"Пауки везде! Беги Рон!");
			$ArrayNum = count($TextArray) - 1;
			$rand = rand (0,$ArrayNum);
			return $TextArray[$rand];
	}
?>