<?php

namespace LiveVoting\QuestionTypes\FreeInput;

use LiveVoting\Display\Bar\xlvoBarFreeInputsGUI;
use LiveVoting\Display\Bar\xlvoBarGroupingCollectionGUI;
use LiveVoting\Option\xlvoOption;
use LiveVoting\QuestionTypes\xlvoInputResultsGUI;
use LiveVoting\Vote\xlvoVote;

/**
 * Class xlvoFreeInputResultsGUI
 *
 * @package LiveVoting\QuestionTypes\FreeInput
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class xlvoFreeInputResultsGUI extends xlvoInputResultsGUI {

	const PARAM_CATEGORIZE = 'categorize';


	/**
	 * @return string
	 * @throws \ilException
	 */
	public function getHTML() {
		$categorize = (bool) filter_input(INPUT_GET, self::PARAM_CATEGORIZE);
//		if ($categorize) {
			return $this->getCategorizeHTML();
//		} else {
//			return $this->getStandardHTML();
//		}
	}


	/**
	 * @param xlvoVote[] $votes
	 *
	 * @return string
	 */
	public function getTextRepresentationForVotes(array $votes) {
		$string_votes = array();
		foreach ($votes as $vote) {
			$string_votes[] = str_replace([ "\r\n", "\r", "\n" ], " ", $vote->getFreeInput());
		}

		return implode(", ", $string_votes);
	}


	/**
	 * @return string
	 * @throws \ilException
	 */
	protected function getStandardHTML() {
		$bars = new xlvoBarGroupingCollectionGUI();
		$bars->setShowTotalVotes(true);

		/**
		 * @var xlvoOption $option
		 */
		$option = $this->manager->getVoting()->getFirstVotingOption();

		/**
		 * @var xlvoVote[] $votes
		 */
		$votes = $this->manager->getVotesOfOption($option->getId());
		foreach ($votes as $vote) {
			$bars->addBar(new xlvoBarFreeInputsGUI($this->manager->getVoting(), $vote));
		}

		$bars->setTotalVotes(count($votes));

		return $bars->getHTML();
	}


	/**
	 * @return string
	 */
	protected function getCategorizeHTML() {
		if (!self::dic()->ctrl()->isAsynch()) {
			self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/node_modules/dragula/dist/dragula.js');
			self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/js/QuestionTypes/FreeInput/xlvoFreeInputCategorize.js');
			self::dic()->mainTemplate()->addOnLoadCode('xlvoFreeInputCategorize.init();');
			self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/node_modules/dragula/dist/dragula.min.css');
			self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/templates/default/QuestionTypes/FreeInput/free_input.css');
		}

		$bars_html = $this->getStandardHTML();
		$tpl = self::plugin()->template('default/QuestionTypes/FreeInput/tpl.free_input_categorize.html');
		$tpl->setVariable('ANSWERS', $bars_html);
		return $tpl->get();
	}
}
