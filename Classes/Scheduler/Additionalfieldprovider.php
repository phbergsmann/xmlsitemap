<?php
class Tx_Xmlsitemap_Scheduler_Additionalfieldprovider implements tx_scheduler_AdditionalFieldProvider
{
	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param	array					Values of the fields from the add/edit task form
	 * @param	tx_scheduler_Task		The task object being eddited. Null when adding a task!
	 * @param	tx_scheduler_Module		Reference to the scheduler backend module
	 * @return	array					A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $schedulerModule) {
		
		$taskInfo['baseURL'] = $task->baseURL;
		$taskInfo['selectedUrlproviders'] = unserialize($task->selectedUrlproviders);
		
		return array(
			'task_xmlsitemap_selectedUrlproviders' => array(
				'code' => $this->getOptionsHTML($taskInfo['selectedUrlproviders']),
				'label' => 'URL-Provider auswählen',
				'cshKey' => '',
				'cshLabel' => ''
			),
			'scheduler_xmlsitemap_baseURL' => array(
				'code' => '<input name="tx_scheduler[scheduler_xmlsitemap_baseURL]" value="' . $taskInfo['baseURL'] . '" />',
				'label' => 'Base-URL auswählen',
				'cshKey' => '',
				'cshLabel' => ''
			),
		);
	}

	private function getOptionsHTML($selectedOptions) {
		$html = '';
		
		foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['xmlsitemap']['urlprovider'] as $urlProviderClass => $options) {
			$selected = (in_array($options['classname'], $selectedOptions)) ? ' selected="selected"' : '';
			$html .= '<option value="' . $options['classname'] . '"' . $selected . '>' . $options['name'] . '</option>';
		}
		
		$urlProvider = '<select name="tx_scheduler[scheduler_xmlsitemap_selectedUrlproviders][]" multiple="multiple" size="5">' . $html . '</select>';
		
		return $urlProvider;
	}
	
	/**
	 * Validates the additional fields' values
	 *
	 * @param	array					An array containing the data submitted by the add/edit task form
	 * @param	tx_scheduler_Module		Reference to the scheduler backend module
	 * @return	boolean					True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $schedulerModule) {
		return true;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param	array					An array containing the data submitted by the add/edit task form
	 * @param	tx_scheduler_Task		Reference to the scheduler backend module
	 * @return	void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->selectedUrlproviders = serialize($submittedData['scheduler_xmlsitemap_selectedUrlproviders']);
		$task->baseURL = $submittedData['scheduler_xmlsitemap_baseURL'];
	}
}
?>