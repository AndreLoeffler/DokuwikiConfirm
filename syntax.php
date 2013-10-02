<?php
/**
 * Plugin googlecal: Inserts an Google Calendar iframe
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Andre Löffler <confirm@andre-loeffler.net>
 * @seealso    (http://www.dokuwiki.org/plugin:iframe)
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_confirm extends DokuWiki_Syntax_Plugin {

    function getType() { return 'substition'; }
    
    function getPType(){ return 'block'; }
    
    function getSort() { return 319; }
    
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{conf>[^}]*?}}', $mode, 'plugin_confirm');
    }

    function handle($match, $state, $pos, &$handler){        
        if(preg_match('/{{conf>(.*)/', $match)) {             // Hook for future features
            // Handle the simplified style of calendar tag
            $match = html_entity_decode(substr($match, 7, -2));
            
            //separate coauthor and confirmation status
            @list($coauth, $status) = explode('|',$match,2);
			//get the name of coauthor
            
			$stylePending = "background-color: red;";
			$styleConfirm = "background-color: green;";

			$style;
			if ($status == "c") {
				$style = $styleConfirm;			
			} else {
				$style = $stylePending;
			}
			
            //builds and fills the data-array
            return array('wiki', hsc(trim($coauth)), hsc(trim($style)));
        } else {
            return array('error', $this->getLang("gcal_Bad_iFrame"));  // this is an error
        } // matched {{conf>...
    }

    function render($mode, &$renderer, $data) {
        list($style, $coauth, $format) = $data;
        
        if($mode == 'xhtml'){
            // Two styles: wiki and error
            switch($style) {
                case 'wiki':
                	$renderer->doc .= "<div style='".$format."'>".$coauth."</div>";
                    break;
                case 'error':
                    $renderer->doc .= "<div class='error'>$url</div>";
                    break;
                default:
                    $renderer->doc .= "<div class='error'>" . $this->getLang('gcal_Invalid_mode') . "</div>";
                    break;
            }
            return true;
        }
        return false;
    }
}
