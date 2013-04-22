<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Charconv Helpers
 * Please make sure that the charset in config is set to iso-8859-1
 *
 * @package		FSC_custom
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rik van Duijn <ik@rikvanduijn.nl>
 */

// --------------------------------------------------------------------

/**
 * Replace all diacritics in a string, and clean the string up for url / filename use.
 * If specified as filename, the "." will not be removed in the cleaning proces.
 *
 * @access	public
 * @param	string	$toClean	the string that needs to be cleaned for URI and filename use.
 * @param	bool	$isFilename	should be TRUE if $toClean is a filename for preservation of the "." in the extension. FALSE by default.
 * @param	bool	$forUrl		indicates if the cleaned string is to be used for Url or Filename use for extra rigid cleaning. TRUE by default.
 * @return	string				$toClean, with replaced diacritics, removed or replaced illegal chars. 
 */
if ( ! function_exists('replace_diacritics'))
{
	function replace_diacritics($toClean, $isFilename = FALSE, $forUrl = TRUE)
	{
		$chars['normalizeChars'] = array(
			'�'=>'S', '�'=>'s', '�'=>'Dj','�'=>'Z', '�'=>'z', '�'=>'A1', '�'=>'A2', '�'=>'A3', '�'=>'A4', '�'=>'A5', 
			'�'=>'A6', '�'=>'AE', '�'=>'C', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I', 
			'�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U', '�'=>'U', 
			'�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss','�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', 
			'�'=>'a', '�'=>'ae', '�'=>'c', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'e', '�'=>'i', '�'=>'i', '�'=>'i', 
			'�'=>'i', '�'=>'o', '�'=>'n', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'o', '�'=>'u', 
			'�'=>'u', '�'=>'u', '�'=>'u', '�'=>'y', '�'=>'y', '�'=>'b', '�'=>'y', '�'=>'f', '�'=>'OE', '�'=>'oe',
			'�'=>'-', '�'=>'Y', '�' => '"', '�' => '"', '�' => '\'', '�' => '\''
		);

		$chars['extraChars'] = array(
			'&'=>'-and-', '+'=>'-plus-', '/'=>'-', '\\'=>'-', '?'=>'-', ':'=>'-', ';'=>'-', '@'=>'-at-', '_'=>'-',
			'='=>'-', '$'=>'-USD-', '�'=>'-EUR-', '*'=>'-', '.'=>'-', ','=>'-', '!'=>'-', '('=>'-', ')'=>'-'
		);

		if($isFilename===TRUE)
		{
			$path_parts = pathinfo($toClean);
			$toClean = $path_parts['filename'];
		}
		$toClean	=	strtr($toClean, $chars['normalizeChars']);				//replace all diacritics
		
		if($forUrl===TRUE)
		{
			$toClean	=	strtr($toClean, $chars['extraChars']);					//replace special characters
			$toClean    =   trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));		//remove other illegal chars
			$toClean    =   str_replace(' ', '-', $toClean);						//replace all spaces
			$toClean    =   str_replace('--', '-', $toClean);						//clean up dashes
			$toClean    =   str_replace('--', '-', $toClean);						//clean up dashes some more
			$toClean	=	url_title($toClean);
		}
		

		if($isFilename===TRUE)
		{
			$toClean = $toClean . "." . $path_parts['extension'];
		}
		
		return $toClean; 
	}
}

/* End of file charconv_helper.php */
/* Location: ./system/helpers/charconv_helper.php */