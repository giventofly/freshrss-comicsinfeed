<?php



/**
 * Class ComicsInFeedExtension
 *
 *
 * Latest version can be found at https://github.com/giventofly/freshrss-comicsinfeed
 *
 * @author Jose Moreira
 */


 //feed parsers loading
require_once __DIR__ . '/comics/loader.php';


class ComicsInFeedExtension extends Minz_Extension{
    public function install()    {
        return true;
    }

    public function uninstall()    {
        return true;
    }

    public function handleConfigureAction()    {
    }

    /**
     * Initialize this extension
     */
    public function init()    {
        // make sure to not run on server without libxml
        if (!extension_loaded('xml')) {
            return;
        }

        //$this->registerHook('entry_before_insert', array($this, 'parseComicsFeed'));
        $this->registerHook('entry_before_display', array($this, 'parseComicsFeed'));
    }


    /**
     * Parse only specifc feeds
     *
     * @param FreshRSS_Entry $entry
     * @return bool
     */
    protected function supports($entry)    {
        $link = $entry->link();

        //buttersafe
        if (!stripos($link, 'buttersafe.com') === false ){
            return 1;
        }
        //the awkward yeti
        if (!stripos($link, 'theawkwardyeti.com') === false ) {
            return 2;
        }
        //buni
        if (!stripos($link, 'bunicomic.com') === false ) {
            return 3;
        }
        //penny-arcade
        if (!stripos($link, 'penny-arcade.com') === false ) {
            return 4;
        }
        //xkcd
        if (!stripos($link, 'xkcd.com') === false ) {
           return 5;
        }
        //explosm
        if (!stripos($link, 'explosm.net') === false ) {
           return 6;
        }
        //monster under the bed
        if (!stripos($link, 'themonsterunderthebed.net') === false ) {
            return 7;
        }
        // Sluggy Freelance
        if (!stripos($link, 'sluggy.com') === false) {
            return 8;
        }
        // Girl Genius
        if (!stripos($link, 'girlgeniusonline.com') === false) {
            return 9;
        }
        // Questionable Content
        if (!stripos($link, 'questionablecontent.net') === false) {
            return 10;
        }
        // Flipside
        if (!stripos($link, 'flipside.gushi.org') === false) {
            return 11;
        }
        // Bouletcorp
        if (!stripos($link, 'bouletcorp.com') === false) {
            return 12;
        }

        return 0;
    }

    /**
     * Parse only before displaying
     *
     * @param FreshRSS_Entry $entry
     * @return mixed
     */
    public function parseComicsFeed($entry){

        $parserInt = $this->supports($entry);
        if ($parserInt == 0) {
            return $entry;
        }

        switch ($parserInt) {
            case 1: {
                $entry = parseButterSafe($entry);
                break;
            }
            case 2: {
                $entry = parseTheAwkwardYeti($entry);
                break;
            }
            case 3: {
                $entry = parseBuni($entry);
                break;
            }
            case 4: {
                $entry = parsePennyArcade($entry);
                break;
            }
            case 5: {
              $entry = parseXkcd($entry);
              break;
            }
            case 6: {
              $entry = parseExplosm($entry);
              break;
            }
            case 7: {
              $entry = parseMonsterUnderBed($entry);
              break;
            }
            case 8: {
                $entry = parseSluggyFreelance($entry);
                break;
            }
            case 9: {
                $entry = parseGirlGenius($entry);
                break;
            }
            case 10: {
                $entry = parseQuestionableContent($entry);
                break;
            }
            case 11: {
                $entry = parseFlipside($entry);
                break;
            }
            case 12: {
                $entry = parseBouletcorp($entry);
                break;
            }
        }

        return $entry;
     }
}
