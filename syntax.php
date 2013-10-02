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
			$styleClient = "background-color: yellow;";
			
			//make pageinfo available
			$info = pageinfo();

			//define default variables
			$style = "border: 1px solid black; width: 400px; height: 30px; ";
			$button = "";

			if ($status == "c") {
				$style .= $styleConfirm;			
			} else {
				$style .= $stylePending;
				$coauth .= " INFO: ".$_SERVER['REMOTE_USER']." bla";
				if ($info['client'] == $coauth) {
					$button .= "<button style='float: right;' onclick='confirm()'>bestätigen</button>";
				}
			}
			
			
			
            //builds and fills the data-array
            return array('wiki', hsc(trim($coauth)), hsc(trim($style)), trim($button));
        } else {
            return array('error', $this->getLang("gcal_Bad_iFrame"));  // this is an error
        } // matched {{conf>...
    }

    function render($mode, &$renderer, $data) {
        list($style, $coauth, $format, $button) = $data;
        
        if($mode == 'xhtml'){
            // Two styles: wiki and error
            switch($style) {
                case 'wiki':
                	$renderer->doc .= "<div style='".$format."'>".$coauth.$button."</div>".
                						"<script type='text/javascript'>".
                							"function confirm() {".
                								"alert('test');".
                							"}".
                						"</script>";
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
