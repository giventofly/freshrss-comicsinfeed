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
        }

        return $entry;
    }
}
