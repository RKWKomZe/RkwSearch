<?php
namespace RKW\RkwSearch\Tests\Hook;

/**
 * Class ImportDatahandlerHookTest
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class ImportDatahandlerHookTest extends \Tx_Phpunit_TestCase {

    /**
     * @var \RKW\RkwSearch\Hooks\ImportDatahandlerHook
     */
    protected $fixture;


    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultNewPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultUpdatedPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultMovedPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultSwappedPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultDeletedPage;



    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedNewPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedUpdatedPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedSwappedPage;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedDeletedPage;




    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultNewContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultUpdatedContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultMovedContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultSwappedContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerDefaultDeletedContent;



    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedNewContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedUpdatedContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedMovedContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedSwappedContent;

    /**
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandlerTranslatedDeletedContent;



    /**
     * Set up fixture
     */
    public function setUp() {


        $this->fixture = new \RKW\RkwSearch\Hooks\ImportDatahandlerHook();
        $this->fixture->setDebugMode(TRUE);

        // set debug data for database queries
        $debugArray = array (
            'getMappedRecordsByPid' => array (
                'tt_content_111_1' => array (
                    0 => array (
                        'header' => 'Test A-111-1',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test B-111-1',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                ),
                'tt_content_111_0' => array (
                    0 => array (
                        'header' => 'Test A-111-0',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test B-111-0',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                ),
                'tt_content_99_1' => array (
                    0 => array (
                        'header' => 'Test A-99-1',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test A-99-1',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                ),
                'tt_content_102_1' => array (
                    0 => array (
                        'header' => 'Test A-102-1',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test B-102-1',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                ),
                'tt_content_102_0' => array (
                    0 => array (
                        'header' => 'Test A-102-0',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test B-102-0',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                ),
                'tt_content_1_0' => array (
                    0 => array (
                        'header' => 'Test A-1-0',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test B-1-0',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                ),
                'tt_content_1_1' => array (
                    0 => array (
                        'header' => 'Test A-1-1',
                        'subheader' => '',
                        'bodytext' => ''
                    ),
                    1 => array (
                        'header' => 'Test B-1-1',
                        'subheader' => '',
                        'bodytext' => ''
                    )
                )
            ),

            'getMappedLanguageOverlayRecordsByUid' => array (
                'pages_language_overlay_111' => array (
                    0 => array (

                        'uid' => 8,
                        'pid' => 111,
                        'tstamp' => 1423572001,
                        'crdate' => 1422346325,
                        'deleted' => 0,
                        'sysLanguageUid' => 1,
                        'cruser_id' => 1,
                        'hidden' => 0,
                        'starttime' => 0,
                        'endtime' => 0,
                        't3ver_oid' => 0,
                        't3ver_state' => 0,
                        'doktype' => 1,
                        'title' => 'Englisch stuff 111',
                        'subtitle' => '',
                        'abstract' => '',
                        'description' => '',
                        'keywords' => ''

                    )
                ),
                'pages_language_overlay_102' => array (
                    0 => array (

                        'uid' => 8,
                        'pid' => 102,
                        'tstamp' => 1423572001,
                        'crdate' => 1422346325,
                        'deleted' => 0,
                        'sysLanguageUid' => 1,
                        'cruser_id' => 1,
                        'hidden' => 0,
                        'starttime' => 0,
                        'endtime' => 0,
                        't3ver_oid' => 0,
                        't3ver_state' => 0,
                        'doktype' => 1,
                        'title' => 'Englisch stuff 102',
                        'subtitle' => '',
                        'abstract' => '',
                        'description' => '',
                        'keywords' => ''

                    )
                ),

                'pages_language_overlay_1' => array (
                     0 => array (

                        'uid' => 10,
                        'pid' => 1,
                        'tstamp' => 1423572001,
                        'crdate' => 1422346325,
                        'deleted' => 0,
                        'sysLanguageUid' => 1,
                        'cruser_id' => 1,
                        'hidden' => 0,
                        'starttime' => 0,
                        'endtime' => 0,
                        't3ver_oid' => 0,
                        't3ver_state' => 0,
                        'doktype' => 1,
                        'title' => 'Englisch stuff 1',
                        'subtitle' => '',
                        'abstract' => '',
                        'description' => '',
                        'keywords' => ''

                     )
                ),
                'pages_language_overlay_99' => array (
                    0 => array (

                        'uid' => 9,
                        'pid' => 99,
                        'tstamp' => 1423572001,
                        'crdate' => 1422346325,
                        'deleted' => 0,
                        'sysLanguageUid' => 1,
                        'cruser_id' => 1,
                        'hidden' => 0,
                        'starttime' => 0,
                        'endtime' => 0,
                        't3ver_oid' => 0,
                        't3ver_state' => 0,
                        'doktype' => 1,
                        'title' => 'Englisch stuff 99',
                        'subtitle' => '',
                        'abstract' => '',
                        'description' => '',
                        'keywords' => ''

                    )
                )

            ),

            'getMappedRecordByUid' => array (
                'pages_1' => array (
                    'uid' => 1,
                    'pid' => 0,
                    'tstamp' => 1423571744,
                    'crdate' => 1418636953,
                    'deleted' => 0,
                    'sorting' => 192,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    't3ver_oid' => 0,
                    't3ver_state' => 0,
                    'doktype' => 1,
                    'title' => 'Testlauf Gründunge',
                    'subtitle' => '',
                    'abstract' => '',
                    'description' => '',
                    'keywords' => ''
                ),
                'pages_111' => array (
                    'uid' => 111,
                    'pid' => 1,
                    'tstamp' => 1423571744,
                    'crdate' => 1418636953,
                    'deleted' => 0,
                    'sorting' => 192,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    't3ver_oid' => 0,
                    't3ver_state' => 0,
                    'doktype' => 1,
                    'title' => 'Testlauf Gründunge',
                    'subtitle' => '',
                    'abstract' => '',
                    'description' => '',
                    'keywords' => ''
                ),
                'pages_102' => array (
                    'uid' => 102,
                    'pid' => 1,
                    'tstamp' => 1423571744,
                    'crdate' => 1418636953,
                    'deleted' => 0,
                    'sorting' => 192,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    't3ver_oid' => 0,
                    't3ver_state' => 0,
                    'doktype' => 1,
                    'title' => 'Testlauf Gründunge',
                    'subtitle' => '',
                    'abstract' => '',
                    'description' => '',
                    'keywords' => ''
                ),
                'pages_language_overlay_8' => array (
                    'uid' => 8,
                    'pid' => 111,
                    'tstamp' => 1423502265,
                    'crdate' => 1423502070,
                    'deleted' => 1,
                    'sysLanguageUid' => 1,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    't3ver_oid' => 0,
                    't3ver_state' => 0,
                    'doktype' => 1,
                    'title' => 'Testpageses',
                    'subtitle' => '',
                    'abstract' => '',
                    'description' => '',
                    'keywords' => ''
                ),
                'pages_language_overlay_10' => array (
                    'uid' => 10,
                    'pid' => 1,
                    'tstamp' => 1423502265,
                    'crdate' => 1423502070,
                    'deleted' => 1,
                    'sysLanguageUid' => 1,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    't3ver_oid' => 0,
                    't3ver_state' => 0,
                    'doktype' => 1,
                    'title' => 'Testpageses',
                    'subtitle' => '',
                    'abstract' => '',
                    'description' => '',
                    'keywords' => ''
                ),
                'tt_content_84' => array (
                    'uid' => 84,
                    'pid' => 111,
                    't3ver_oid' => 0,
                    't3ver_id' => 0,
                    't3ver_wsid' => 0,
                    't3ver_label' => '',
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    't3ver_count' => 0,
                    't3ver_tstamp' => 0,
                    't3ver_move_id' => 0,
                    't3_origuid' => 0,
                    'tstamp' => 1423550415,
                    'crdate' => 1423550415,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'sorting' => 256,
                    'CType' => 'text',
                    'header' => 'Testheadline',
                    'header_position' => '',
                    'bodytext' => 'Testtext',
                    'image' => '',
                    'imagewidth' => 0,
                    'imageorient' => 0,
                    'imagecaption' => '',
                    'imagecols' => 2,
                    'imageborder' => 0,
                    'media' => '',
                    'layout' => 0,
                    'deleted' => 0,
                    'cols' => 0,
                    'records' => '',
                    'pages' => '',
                    'starttime' => 0,
                    'endtime' => 0,
                    'colPos' => 0,
                    'subheader' => '',
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'fe_group' => '',
                    'header_link' => '',
                    'imagecaption_position' => '',
                    'image_link' => '',
                    'image_zoom' => 0,
                    'image_noRows' => 0,
                    'image_effects' => 0,
                    'image_compression' => 0,
                    'altText' => '',
                    'titleText' => '',
                    'longdescURL' => '',
                    'header_layout' => 0,
                    'menu_type' => 0,
                    'list_type' => '',
                    'table_border' => 0,
                    'table_cellspacing' => 0,
                    'table_cellpadding' => 0,
                    'table_bgColor' => 0,
                    'select_key' => '',
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'file_collections' => '',
                    'filelink_size' => 0,
                    'filelink_sorting' => '',
                    'target' => '',
                    'section_frame' => 0,
                    'date' => 0,
                    'multimedia' => '',
                    'image_frames' => 0,
                    'recursive' => 0,
                    'imageheight' => 0,
                    'rte_enabled' => 0,
                    'sys_language_uid' => 0,
                    'pi_flexform' => '',
                    'accessibility_title' => '',
                    'accessibility_bypass' => 0,
                    'accessibility_bypass_text' => '',
                    'l18n_parent' => 0,
                    'selected_categories' => '',
                    'category_field' => '',
                    'categories' => 0,
                ),
                'tt_content_83' => array (
                    'uid' => 83,
                    'pid' => 102,
                    't3ver_oid' => 0,
                    't3ver_id' => 0,
                    't3ver_wsid' => 0,
                    't3ver_label' => '',
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    't3ver_count' => 0,
                    't3ver_tstamp' => 0,
                    't3ver_move_id' => 0,
                    't3_origuid' => 0,
                    'tstamp' => 1423550415,
                    'crdate' => 1423550415,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'sorting' => 256,
                    'CType' => 'text',
                    'header' => 'Testheadline',
                    'header_position' => '',
                    'bodytext' => 'Testtext',
                    'image' => '',
                    'imagewidth' => 0,
                    'imageorient' => 0,
                    'imagecaption' => '',
                    'imagecols' => 2,
                    'imageborder' => 0,
                    'media' => '',
                    'layout' => 0,
                    'deleted' => 0,
                    'cols' => 0,
                    'records' => '',
                    'pages' => '',
                    'starttime' => 0,
                    'endtime' => 0,
                    'colPos' => 0,
                    'subheader' => '',
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'fe_group' => '',
                    'header_link' => '',
                    'imagecaption_position' => '',
                    'image_link' => '',
                    'image_zoom' => 0,
                    'image_noRows' => 0,
                    'image_effects' => 0,
                    'image_compression' => 0,
                    'altText' => '',
                    'titleText' => '',
                    'longdescURL' => '',
                    'header_layout' => 0,
                    'menu_type' => 0,
                    'list_type' => '',
                    'table_border' => 0,
                    'table_cellspacing' => 0,
                    'table_cellpadding' => 0,
                    'table_bgColor' => 0,
                    'select_key' => '',
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'file_collections' => '',
                    'filelink_size' => 0,
                    'filelink_sorting' => '',
                    'target' => '',
                    'section_frame' => 0,
                    'date' => 0,
                    'multimedia' => '',
                    'image_frames' => 0,
                    'recursive' => 0,
                    'imageheight' => 0,
                    'rte_enabled' => 0,
                    'sys_language_uid' => 0,
                    'pi_flexform' => '',
                    'accessibility_title' => '',
                    'accessibility_bypass' => 0,
                    'accessibility_bypass_text' => '',
                    'l18n_parent' => 0,
                    'selected_categories' => '',
                    'category_field' => '',
                    'categories' => 0,
                ),
                'tt_content_88' => array (
                    'uid' => 88,
                    'pid' => 111,
                    't3ver_oid' => 0,
                    't3ver_id' => 0,
                    't3ver_wsid' => 0,
                    't3ver_label' => '',
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    't3ver_count' => 0,
                    't3ver_tstamp' => 0,
                    't3ver_move_id' => 0,
                    't3_origuid' => 0,
                    'tstamp' => 1423555955,
                    'crdate' => 1423555955,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'sorting' => 128,
                    'CType' => 'text',
                    'header' => 'English Content',
                    'header_position' => '',
                    'bodytext' => 'English Content',
                    'image' => '',
                    'imagewidth' => 0,
                    'imageorient' => 0,
                    'imagecaption' => '',
                    'imagecols' => 2,
                    'imageborder' => 0,
                    'media' => '',
                    'layout' => 0,
                    'deleted' => 0,
                    'cols' => 0,
                    'records' => '',
                    'pages' => '',
                    'starttime' => 0,
                    'endtime' => 0,
                    'colPos' => 0,
                    'subheader' => '',
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'fe_group' => '',
                    'header_link' => '',
                    'imagecaption_position' => '',
                    'image_link' => '',
                    'image_zoom' => 0,
                    'image_noRows' => 0,
                    'image_effects' => 0,
                    'image_compression' => 0,
                    'altText' => '',
                    'titleText' => '',
                    'longdescURL' => '',
                    'header_layout' => 0,
                    'menu_type' => 0,
                    'list_type' => '',
                    'table_border' => 0,
                    'table_cellspacing' => 0,
                    'table_cellpadding' => 0,
                    'table_bgColor' => 0,
                    'select_key' => '',
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'file_collections' => '',
                    'filelink_size' => 0,
                    'filelink_sorting' => '',
                    'target' => '',
                    'section_frame' => 0,
                    'date' => 0,
                    'multimedia' => '',
                    'image_frames' => 0,
                    'recursive' => 0,
                    'imageheight' => 0,
                    'rte_enabled' => 0,
                    'sysLanguageUid' => 1,
                    'tx_impexp_origuid' => 0,
                    'pi_flexform' => '',
                    'accessibility_title' => '',
                    'accessibility_bypass' => 0,
                    'accessibility_bypass_text' => '',
                    'l18n_parent' => 0,
                    'selected_categories' => '',
                    'category_field' => '',
                    'categories' => 0,
                ),
                'tt_content_89' => array (
                    'uid' => 89,
                    'pid' => 102,
                    't3ver_oid' => 0,
                    't3ver_id' => 0,
                    't3ver_wsid' => 0,
                    't3ver_label' => '',
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    't3ver_count' => 0,
                    't3ver_tstamp' => 0,
                    't3ver_move_id' => 0,
                    't3_origuid' => 0,
                    'tstamp' => 1423555955,
                    'crdate' => 1423555955,
                    'cruser_id' => 1,
                    'hidden' => 0,
                    'sorting' => 128,
                    'CType' => 'text',
                    'header' => 'English Content',
                    'header_position' => '',
                    'bodytext' => 'English Content',
                    'image' => '',
                    'imagewidth' => 0,
                    'imageorient' => 0,
                    'imagecaption' => '',
                    'imagecols' => 2,
                    'imageborder' => 0,
                    'media' => '',
                    'layout' => 0,
                    'deleted' => 0,
                    'cols' => 0,
                    'records' => '',
                    'pages' => '',
                    'starttime' => 0,
                    'endtime' => 0,
                    'colPos' => 0,
                    'subheader' => '',
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'fe_group' => '',
                    'header_link' => '',
                    'imagecaption_position' => '',
                    'image_link' => '',
                    'image_zoom' => 0,
                    'image_noRows' => 0,
                    'image_effects' => 0,
                    'image_compression' => 0,
                    'altText' => '',
                    'titleText' => '',
                    'longdescURL' => '',
                    'header_layout' => 0,
                    'menu_type' => 0,
                    'list_type' => '',
                    'table_border' => 0,
                    'table_cellspacing' => 0,
                    'table_cellpadding' => 0,
                    'table_bgColor' => 0,
                    'select_key' => '',
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'file_collections' => '',
                    'filelink_size' => 0,
                    'filelink_sorting' => '',
                    'target' => '',
                    'section_frame' => 0,
                    'date' => 0,
                    'multimedia' => '',
                    'image_frames' => 0,
                    'recursive' => 0,
                    'imageheight' => 0,
                    'rte_enabled' => 0,
                    'sysLanguageUid' => 1,
                    'tx_impexp_origuid' => 0,
                    'pi_flexform' => '',
                    'accessibility_title' => '',
                    'accessibility_bypass' => 0,
                    'accessibility_bypass_text' => '',
                    'l18n_parent' => 0,
                    'selected_categories' => '',
                    'category_field' => '',
                    'categories' => 0,
                )
            )
        );
        $this->fixture->setDebugArray($debugArray);


        //=================================================
        /*
         * Inserted new page in page with uid = 1 (is parent)
         * and under page 102
         */
        //=================================================
        $this->dataHandlerDefaultNewPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultNewPage->substNEWwithIDs = array ( 'NEW54d8dc268badf' => 111);
        $this->dataHandlerDefaultNewPage->cmdmap = array ();
        $this->dataHandlerDefaultNewPage->checkValue_currentRecord = array (
       
            'doktype' => 1,
            'hidden' => 0,
            'starttime' => 0,
            'endtime' => 0,
            'layout' => 0,
            'url_scheme' => 0,
            'urltype' => 1,
            'lastUpdated' => 0,
            'newUntil' => 0,
            'cache_timeout' => 0,
            'shortcut_mode' => 0,
            'module' => '',
            'perms_userid' => 1,
            'perms_groupid' => 0,
            'perms_user' => 31,
            'perms_group' => 27,
            'perms_everybody' => 0,
            'pid' => -102,
            'sorting' => 288,
            'title' => 'Testseiten',
            'nav_title' => '',
            'subtitle' => '',
            'nav_hide' => 0,
            'extendToSubpages' => 0,
            'fe_group' => '',
            'fe_login_mode' => 0,
            'abstract' => '',
            'keywords' => '',
            'description' => '',
            'backend_layout' => '',
            'backend_layout_next_level' => '',
            'content_from_pid' => '',
            'alias' => '',
            'target' => '',
            'cache_tags' => '',
            'no_cache' => 0,
            'l18n_cfg' => 0,
            'is_siteroot' => 0,
            'no_search' => 0,
            'editlock' => 0,
            'php_tree_stop' => 0,
            'media' => '',
            'storage_pid' => '',
            'TSconfig' => '',
            'categories' => '',
        );

        $this->dataHandlerDefaultNewPage->datamap = array (
        
            'pages' => array (

                'NEW54d8dc268badf' => array (

                    'doktype' => 1,
                    'title' => 'Testseiten',
                    'nav_title' => '',
                    'subtitle' => '',
                    'hidden' => 0,
                    'nav_hide' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'extendToSubpages' => 0,
                    'fe_group' => '',
                    'fe_login_mode' => 0,
                    'abstract' => '',
                    'keywords' => '',
                    'description' => '',
                    'lastUpdated' => 0,
                    'layout' => 0,
                    'newUntil' => 0,
                    'backend_layout' => '',
                    'backend_layout_next_level' => '',
                    'content_from_pid' => '',
                    'alias' => '',
                    'target' => '',
                    'url_scheme' => 0,
                    'cache_timeout' => 0,
                    'cache_tags' => '',
                    'no_cache' => 0,
                    'l18n_cfg' => 0,
                    'is_siteroot' => 0,
                    'no_search' => 0,
                    'editlock' => 0,
                    'php_tree_stop' => 0,
                    'module' => '',
                    'media' => '',
                    'storage_pid' => '',
                    'TSconfig' => '',
                    'categories' => '',
                    'pid' => -102,
                ),

            )

        );


        //=================================================
        /*
         * Updated page with uid = 111
         */
        //=================================================
        $this->dataHandlerDefaultUpdatedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultUpdatedPage->substNEWwithIDs = array ();
        $this->dataHandlerDefaultUpdatedPage->cmdmap = array ();
        $this->dataHandlerDefaultUpdatedPage->checkValue_currentRecord = array (

            'uid' => 111,
            'pid' => 1,
            't3ver_oid' => 0,
            't3ver_id' => 0,
            't3ver_wsid' => 0,
            't3ver_label' => '',
            't3ver_state' => 0,
            't3ver_stage' => 0,
            't3ver_count' => 0,
            't3ver_tstamp' => 0,
            't3ver_move_id' => 0,
            't3_origuid' => 0,
            'tstamp' => 1423500052,
            'sorting' => 240,
            'deleted' => 0,
            'perms_userid' => 1,
            'perms_groupid' => 0,
            'perms_user' => 31,
            'perms_group' => 27,
            'perms_everybody' => 0,
            'editlock' => 0,
            'crdate' => 1423500052,
            'cruser_id' => 1,
            'hidden' => 0,
            'title' => 'Testseiten',
            'doktype' => 1,
            'TSconfig' => '',
            'storage_pid' => 0,
            'is_siteroot' => 0,
            'php_tree_stop' => 0,
            'url' => '',
            'starttime' => 0,
            'endtime' => 0,
            'urltype' => 1,
            'shortcut' => 0,
            'shortcut_mode' => 0,
            'no_cache' => 0,
            'fe_group' => '',
            'subtitle' => '',
            'layout' => 0,
            'url_scheme' => 0,
            'target' => '',
            'media' => 0,
            'lastUpdated' => 0,
            'keywords' => '',
            'cache_timeout' => 0,
            'cache_tags' => '',
            'newUntil' => 0,
            'description' => '',
            'no_search' => 0,
            'SYS_LASTCHANGED' => 0,
            'abstract' => '',
            'module' => '',
            'extendToSubpages' => 0,
            'author' => '',
            'author_email' => '',
            'nav_title' => '',
            'nav_hide' => 0,
            'content_from_pid' => 0,
            'mount_pid' => 0,
            'mount_pid_ol' => 0,
            'alias' => '',
            'l18n_cfg' => 0,
            'fe_login_mode' => 0,
            'backend_layout' => '',
            'backend_layout_next_level' => '',
            'categories' => 0
        );

        $this->dataHandlerDefaultUpdatedPage->datamap = array (

            'pages' => array (

                '111' => array (
                    'doktype' => 1,
                    'title' => 'Testseiten',
                    'nav_title' => '',
                    'subtitle' => '',
                    'hidden' => 0,
                    'nav_hide' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'extendToSubpages' => 0,
                    'fe_group' => '',
                    'fe_login_mode' => 0,
                    'abstract' => '',
                    'keywords' => '',
                    'description' => '',
                    'lastUpdated' => 0,
                    'layout' => 0,
                    'newUntil' => 0,
                    'backend_layout' => '',
                    'backend_layout_next_level' => '',
                    'content_from_pid' => '',
                    'alias' => '',
                    'target' => '',
                    'url_scheme' => 0,
                    'cache_timeout' => 0,
                    'cache_tags' => '',
                    'no_cache' => 0,
                    'l18n_cfg' => 0,
                    'is_siteroot' => 0,
                    'no_search' => 0,
                    'editlock' => 0,
                    'php_tree_stop' => 0,
                    'module' => '',
                    'media' => '',
                    'storage_pid' => '',
                    'TSconfig' => '',
                    'categories' => '',
                )
            )
        );

        //=================================================
        /*
         * Moved page with uid = 111 from under uid=102 in uid=1 into uid=99 under uid=95
         */
        //=================================================
        $this->dataHandlerDefaultMovedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultMovedPage->substNEWwithIDs = array ();
        $this->dataHandlerDefaultMovedPage->cmdmap = array (
            'pages' => array (
                '111' => array (
                    'move' => 99
                )
            )
        );

        $this->dataHandlerDefaultMovedPage->checkValue_currentRecord = array ();
        $this->dataHandlerDefaultMovedPage->datamap = array ();


        //=================================================
        /*
         * Swapped a drafted version of page with uid = 111 to live
         */
        //=================================================
        $this->dataHandlerDefaultSwappedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultSwappedPage->substNEWwithIDs = array ();
        $this->dataHandlerDefaultSwappedPage->cmdmap = array (
            'pages' => array (
                '111' => array (
                    'version' => array (
                        'action' => 'swap',
                        'swapWith' => 112,
                        'comment' => '',
                        'notificationAlternativeRecipients' => array ()
                    )
                )
            )
        );

        $this->dataHandlerDefaultSwappedPage->checkValue_currentRecord = array ();
        $this->dataHandlerDefaultSwappedPage->datamap = array ();


        //=================================================
        /*
         * Deleted translated page with uid = 111
         */
        //=================================================
        $this->dataHandlerDefaultDeletedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultDeletedPage->substNEWwithIDs = array ();
        $this->dataHandlerDefaultDeletedPage->cmdmap = array (
            'pages' => array (
                '111' => array (
                    'delete' => 1
                )
            )
        );

        $this->dataHandlerDefaultDeletedPage->checkValue_currentRecord = array ();
        $this->dataHandlerDefaultDeletedPage->datamap = array ();



        //=================================================
        /*
         * Inserted translation for page with uid = 111 --> new uid = 8
         */
        //=================================================
        $this->dataHandlerTranslatedNewPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedNewPage->substNEWwithIDs = array ( 'NEW54d8e0c85c859' => 8);
        $this->dataHandlerTranslatedNewPage->cmdmap = array ();
        $this->dataHandlerTranslatedNewPage->checkValue_currentRecord = array (
            'doktype' => 1,
            'hidden' => 0,
            'starttime' => 0,
            'endtime' => 0,
            'urltype' => 1,
            'shortcut_mode' => 0,
            'pid' => 111,
            'title' => 'Testpage',
            'nav_title' => '',
            'subtitle' => '',
            'abstract' => '',
            'keywords' => '',
            'description' => '',
            'author' => '',
            'author_email' => '',
            'media' => '',
            'sys_language_uid' => 1            
            
        );

        $this->dataHandlerTranslatedNewPage->datamap = array (
            'pages_language_overlay' => array (
                'NEW54d8e0c85c859' => Array (

                    'title' => 'Testpage',
                    'nav_title' => '',
                    'subtitle' => '',
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'abstract' => '',
                    'keywords' => '',
                    'description' => '',
                    'author' => '',
                    'author_email' => '',
                    'media' => '',
                    'doktype' => 1,
                    'sys_language_uid' => 1,
                    'pid' => 111,
                )
            )
        );

        //=================================================
        /*
         * Updated translation for page with uid = 8
         */
        //=================================================
        $this->dataHandlerTranslatedUpdatedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedUpdatedPage->substNEWwithIDs = array ( );
        $this->dataHandlerTranslatedUpdatedPage->cmdmap = array ();
        $this->dataHandlerTranslatedUpdatedPage->checkValue_currentRecord = array (
            'uid' => 8,
            'pid' => 111,
            'doktype' => 1,
            't3ver_oid' => 0,
            't3ver_id' => 0,
            't3ver_wsid' => 0,
            't3ver_label' => '',
            't3ver_state' => 0,
            't3ver_stage' => 0,
            't3ver_count' => 0,
            't3ver_tstamp' => 0,
            't3_origuid' => 0,
            'tstamp' => 1423501316,
            'crdate' => 1423501316,
            'cruser_id' => 1,
            'sys_language_uid' => 1,
            'title' => 'Testpage',
            'hidden' => 0,
            'starttime' => 0,
            'endtime' => 0,
            'deleted' => 0,
            'subtitle' => '',
            'nav_title' => '',
            'media' => 0,
            'keywords' => '',
            'description' => '',
            'abstract' => '',
            'author' => '',
            'author_email' => '',
            'url' => '',
            'urltype' => 1,
            'shortcut' => 0,
            'shortcut_mode' => 0,
        );

        $this->dataHandlerTranslatedUpdatedPage->datamap = array (
            'pages_language_overlay' => array (
                'NEW54d8e0c85c859' => array (
                    'doktype' => 1,
                    'title' => 'Testpages',
                    'nav_title' => '',
                    'subtitle' => '',
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'abstract' => '',
                    'keywords' => '',
                    'description' => '',
                    'author' => '',
                    'author_email' => '',
                    'media' => '',
                    'sys_language_uid' => 1,
                )
            )
        );


        //=================================================
        /*
         * Swapped a drafted version of translated page with uid = 8
         */
        //=================================================
        $this->dataHandlerTranslatedSwappedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedSwappedPage->substNEWwithIDs = array ();
        $this->dataHandlerTranslatedSwappedPage->cmdmap = array (
            'pages_language_overlay' => array (
                '8' => array (
                    'version' => array (
                        'action' => 'swap',
                        'swapWith' => 9,
                        'comment' => '',
                        'notificationAlternativeRecipients' => array ()
                    )
                )
            )
        );

        $this->dataHandlerTranslatedSwappedPage->checkValue_currentRecord = array ();
        $this->dataHandlerTranslatedSwappedPage->datamap = array ();

        //=================================================
        /*
         * Deleted translated page with uid = 8
         */
        //=================================================
        $this->dataHandlerTranslatedDeletedPage = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedDeletedPage->substNEWwithIDs = array ();
        $this->dataHandlerTranslatedDeletedPage->cmdmap = array (
            'pages_language_overlay' => array (
                '8' => array (
                    'delete' => 1
                )
            )

        );

        $this->dataHandlerTranslatedDeletedPage->checkValue_currentRecord = array ();
        $this->dataHandlerTranslatedDeletedPage->datamap = array ();


        //=================================================
        /*
         * Inserted new tt_content with uid = 84 on page uid = 111
         */
        //=================================================
        $this->dataHandlerDefaultNewContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultNewContent->substNEWwithIDs = array ( 'NEW54d9a7c52bdb7' => 84);
        $this->dataHandlerDefaultNewContent->cmdmap = array ();
        $this->dataHandlerDefaultNewContent->checkValue_currentRecord = array (
            'CType' => 'text',
            'starttime' => 0,
            'endtime' => 0,
            'layout' => 0,
            'colPos' => 0,
            'date' => 0,
            'header_position' => '',
            'header_layout' => 0,
            'imagewidth' => 0,
            'imageheight' => 0,
            'imageorient' => 0,
            'imagecols' => 2,
            'imagecaption_position' => '',
            'cols' => 0,
            'recursive' => 0,
            'menu_type' => 0,
            'list_type' => '',
            'table_bgColor' => 0,
            'table_border' => 0,
            'table_cellspacing' => 0,
            'table_cellpadding' => 0,
            'target' => '',
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'section_frame' => 0,
            'sectionIndex' => 1,
            'accessibility_title' => '',
            'accessibility_bypass_text' => '',
            'sorting' => 256,
            'pid' => 111,
            'sys_language_uid' => 0,
            'header' => 'Testheadline',
            'header_link' => '',
            '_TRANSFORM_bodytext' => 'RTE',
            'bodytext' => 'Testtext',
            'rte_enabled' => 0,
            'hidden' => 0,
            'linkToTop' => 0,
            'fe_group' => '',
            'categories' => ''
        );

        $this->dataHandlerDefaultNewContent->datamap = array (

            'tt_content' => array (
                'NEW54d9a7c52bdb7' => array (
                    'CType' => 'text',
                    'colPos' => 0,
                    'sys_language_uid' => 0,
                    'header' => 'Testheadline',
                    'header_layout' => 0,
                    'header_position' => '',
                    'date' => 0,
                    'header_link' => '',
                    '_TRANSFORM_bodytext' => 'RTE',
                    'bodytext' => 'Testtext',
                    'rte_enabled' => 0,
                    'layout' => 0,
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'section_frame' => 0,
                    'hidden' => 0,
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'fe_group' => '',
                    'categories' => '',
                    'pid' => 111,
                )
            )
        );

        //=================================================
        /*
         * Updated tt_content with uid = 84 on page uid = 111
         */
        //=================================================
        $this->dataHandlerDefaultUpdatedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultUpdatedContent->substNEWwithIDs = array ();
        $this->dataHandlerDefaultUpdatedContent->cmdmap = array ();
        $this->dataHandlerDefaultUpdatedContent->checkValue_currentRecord = array (
            'uid' => 84,
            'pid' => 111,
            't3ver_oid' => 0,
            't3ver_id' => 0,
            't3ver_wsid' => 0,
            't3ver_label' => '',
            't3ver_state' => 0,
            't3ver_stage' => 0,
            't3ver_count' => 0,
            't3ver_tstamp' => 0,
            't3ver_move_id' => 0,
            't3_origuid' => 0,
            'tstamp' => 1423550415,
            'crdate' => 1423550415,
            'cruser_id' => 1,
            'hidden' => 0,
            'sorting' => 256,
            'CType' => 'text',
            'header' => 'Testheadline',
            'header_position' => '',
            'bodytext' => 'Testtext',
            'image' => '',
            'imagewidth' => 0,
            'imageorient' => 0,
            'imagecaption' => '',
            'imagecols' => 2,
            'imageborder' => 0,
            'media' => '',
            'layout' => 0,
            'deleted' => 0,
            'cols' => 0,
            'records' => '',
            'pages' => '',
            'starttime' => 0,
            'endtime' => 0,
            'colPos' => 0,
            'subheader' => '',
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'fe_group' => '',
            'header_link' => '',
            'imagecaption_position' => '',
            'image_link' => '',
            'image_zoom' => 0,
            'image_noRows' => 0,
            'image_effects' => 0,
            'image_compression' => 0,
            'altText' => '',
            'titleText' => '',
            'longdescURL' => '',
            'header_layout' => 0,
            'menu_type' => 0,
            'list_type' => '',
            'table_border' => 0,
            'table_cellspacing' => 0,
            'table_cellpadding' => 0,
            'table_bgColor' => 0,
            'select_key' => '',
            'sectionIndex' => 1,
            'linkToTop' => 0,
            'file_collections' => '',
            'filelink_size' => 0,
            'filelink_sorting' => '',
            'target' => '',
            'section_frame' => 0,
            'date' => 0,
            'multimedia' => '',
            'image_frames' => 0,
            'recursive' => 0,
            'imageheight' => 0,
            'rte_enabled' => 0,
            'sys_language_uid' => 0,
            'pi_flexform' => '',
            'accessibility_title' => '',
            'accessibility_bypass' => 0,
            'accessibility_bypass_text' => '',
            'l18n_parent' => 0,
            'selected_categories' => '',
            'category_field' => '',
            'categories' => 0,
        );

        $this->dataHandlerDefaultUpdatedContent->datamap = array (

            'tt_content' => array (
                '84' => array (
                    'CType' => 'text',
                    'colPos' => 0,
                    'sys_language_uid' => 0,
                    'header' => 'Testheadlines',
                    'header_layout' => 0,
                    'header_position' => '',
                    'date' => 0,
                    'header_link' => '',
                    '_TRANSFORM_bodytext' => 'RTE',
                    'bodytext' => '<p>Testtext</p>',
                    'rte_enabled' => 0,
                    'layout' => 0,
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'section_frame' => 0,
                    'hidden' => 0,
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'fe_group' => '',
                    'categories' => '',
                )
            )
        );

        //=================================================
        /*
         * Moved content with uid = 84 from page with uid = 111 to page with uid = 102
         */
        //=================================================
        $this->dataHandlerDefaultMovedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultMovedContent->substNEWwithIDs = array ();
        $this->dataHandlerDefaultMovedContent->cmdmap = array ();

        $this->dataHandlerDefaultMovedContent->checkValue_currentRecord = array (
            'uid' => 83,
            'pid' => 102,
            't3ver_oid' => 0,
            't3ver_id' => 0,
            't3ver_wsid' => 0,
            't3ver_label' => '',
            't3ver_state' => 0,
            't3ver_stage' => 0,
            't3ver_count' => 0,
            't3ver_tstamp' => 0,
            't3ver_move_id' => 0,
            't3_origuid' => 0,
            'tstamp' => 1423551543,
            'crdate' => 1423550415,
            'cruser_id' => 1,
            'hidden' => 0,
            'sorting' => 8,
            'CType' => 'text',
            'header' => 'Testheadlines',
            'header_position' => '',
            'bodytext' => 'Testtext',
            'image' => '',
            'imagewidth' => 0,
            'imageorient' => 0,
            'imagecaption' => '',
            'imagecols' => 2,
            'imageborder' => 0,
            'media' => '',
            'layout' => 0,
            'deleted' => 0,
            'cols' => 0,
            'records' => '',
            'pages' => '',
            'starttime' => 0,
            'endtime' => 0,
            'colPos' => 0,
            'subheader' => '',
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'fe_group' => '',
            'header_link' => '',
            'imagecaption_position' => '',
            'image_link' => '',
            'image_zoom' => 0,
            'image_noRows' => 0,
            'image_effects' => 0,
            'image_compression' => 0,
            'altText' => '',
            'titleText' => '',
            'longdescURL' => '',
            'header_layout' => 0,
            'menu_type' => 0,
            'list_type' => '',
            'table_border' => 0,
            'table_cellspacing' => 0,
            'table_cellpadding' => 0,
            'table_bgColor' => 0,
            'select_key' => '',
            'sectionIndex' => 1,
            'linkToTop' => 0,
            'file_collections' => '',
            'filelink_size' => 0,
            'filelink_sorting' => '',
            'target' => '',
            'section_frame' => 0,
            'date' => 0,
            'multimedia' => '',
            'image_frames' => 0,
            'recursive' => 0,
            'imageheight' => 0,
            'rte_enabled' => 0,
            'sys_language_uid' => 0,
            'pi_flexform' => '',
            'accessibility_title' => '',
            'accessibility_bypass' => 0,
            'accessibility_bypass_text' => '',
            'l18n_parent' => 0,
            'selected_categories' => '',
            'category_field' => '',
            'categories' => 0,
        );


        $this->dataHandlerDefaultMovedContent->datamap = array (
            'tt_content' => array (
                '83' => array (
                    'colPos' => 0,
                    'sys_language_uid' => 0,
                )
            )
        );

        //=================================================
        /*
         * Swapped content with uid = 87 to uid = 84 on page uid = 111
         */
        //=================================================
        $this->dataHandlerDefaultSwappedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultSwappedContent->substNEWwithIDs = array ();
        $this->dataHandlerDefaultSwappedContent->cmdmap = array (

            'tt_content' => array (
                '84' => array (
                    'version' => array (
                        'action' => 'swap',
                        'swapWith' => 87,
                        'comment' => '',
                        'notificationAlternativeRecipients' => array ()
                    )
                )
            )

        );

        $this->dataHandlerDefaultSwappedContent->checkValue_currentRecord = array ();
        $this->dataHandlerDefaultSwappedContent->datamap = array ();

        //=================================================
        /*
         * Deleted content with uid = 84 on page uid = 111
         */
        //=================================================
        $this->dataHandlerDefaultDeletedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerDefaultDeletedContent->substNEWwithIDs = array ();
        $this->dataHandlerDefaultDeletedContent->cmdmap = array (

            'tt_content' => array (
                '84' => array (
                    'delete' => 1
                )
            )

        );
        $this->dataHandlerDefaultDeletedContent->checkValue_currentRecord = array ();
        $this->dataHandlerDefaultDeletedContent->datamap = array ();


        //=================================================
        /*
         * Inserted new translation in tt_content with uid = 88 on page uid = 111
         */
        //=================================================
        $this->dataHandlerTranslatedNewContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedNewContent->substNEWwithIDs = array ( 'NEW54d9bd6a95257' => 88);
        $this->dataHandlerTranslatedNewContent->cmdmap = array ();
        $this->dataHandlerTranslatedNewContent->checkValue_currentRecord = array (
            'CType' => 'text',
            'starttime' => 0,
            'endtime' => 0,
            'layout' => 0,
            'colPos' => 0,
            'date' => 0,
            'header_position' => '',
            'header_layout' => 0,
            'imagewidth' => 0,
            'imageheight' => 0,
            'imageorient' => 0,
            'imagecols' => 2,
            'imagecaption_position' => '',
            'cols' => 0,
            'recursive' => 0,
            'menu_type' => 0,
            'list_type' => '',
            'table_bgColor' => 0,
            'table_border' => 0,
            'table_cellspacing' => 0,
            'table_cellpadding' => 0,
            'target' => '',
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'section_frame' => 0,
            'sectionIndex' => 1,
            'accessibility_title' => '',
            'accessibility_bypass_text' => '',
            'sorting' => 128,
            'pid' => 111,
            'sys_language_uid' => 1,
            'l18n_parent' => 0,
            'header' => 'English Content',
            'header_link' => '',
            '_TRANSFORM_bodytext' => 'RTE',
            'bodytext' => 'English Content',
            'rte_enabled' => 0,
            'hidden' => 0,
            'linkToTop' => 0,
            'fe_group' => '',
            'categories' => ''
        );

        $this->dataHandlerTranslatedNewContent->datamap = array (
            'tt_content' => array (
                'NEW54d9bd6a95257' => array (
                    'CType' => 'text',
                    'colPos' => 0,
                    'sys_language_uid' => 1,
                    'l18n_parent' => 0,
                    'header' => 'English Content',
                    'header_layout' => 0,
                    'header_position' => '',
                    'date' => 0,
                    'header_link' => '',
                    '_TRANSFORM_bodytext' => 'RTE',
                    'bodytext' => 'English Content',
                    'rte_enabled' => 0,
                    'layout' => 0,
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'section_frame' => 0,
                    'hidden' => 0,
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'fe_group' => '',
                    'categories' => '',
                    'pid' => 111,
                )
            )
        );

        //=================================================
        /*
         * Updated tt_content with uid = 84 on page uid = 111
         */
        //=================================================
        $this->dataHandlerTranslatedUpdatedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedUpdatedContent->substNEWwithIDs = array ();
        $this->dataHandlerTranslatedUpdatedContent->cmdmap = array ();
        $this->dataHandlerTranslatedUpdatedContent->checkValue_currentRecord = array (
            'uid' => 88,
            'pid' => 111,
            't3ver_oid' => 0,
            't3ver_id' => 0,
            't3ver_wsid' => 0,
            't3ver_label' => '',
            't3ver_state' => 0,
            't3ver_stage' => 0,
            't3ver_count' => 0,
            't3ver_tstamp' => 0,
            't3ver_move_id' => 0,
            't3_origuid' => 0,
            'tstamp' => 1423555955,
            'crdate' => 1423555955,
            'cruser_id' => 1,
            'hidden' => 0,
            'sorting' => 128,
            'CType' => 'text',
            'header' => 'English Content',
            'header_position' => '',
            'bodytext' => 'English Content',
            'image' => '',
            'imagewidth' => 0,
            'imageorient' => 0,
            'imagecaption' => '',
            'imagecols' => 2,
            'imageborder' => 0,
            'media' => '',
            'layout' => 0,
            'deleted' => 0,
            'cols' => 0,
            'records' => '',
            'pages' => '',
            'starttime' => 0,
            'endtime' => 0,
            'colPos' => 0,
            'subheader' => '',
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'fe_group' => '',
            'header_link' => '',
            'imagecaption_position' => '',
            'image_link' => '',
            'image_zoom' => 0,
            'image_noRows' => 0,
            'image_effects' => 0,
            'image_compression' => 0,
            'altText' => '',
            'titleText' => '',
            'longdescURL' => '',
            'header_layout' => 0,
            'menu_type' => 0,
            'list_type' => '',
            'table_border' => 0,
            'table_cellspacing' => 0,
            'table_cellpadding' => 0,
            'table_bgColor' => 0,
            'select_key' => '',
            'sectionIndex' => 1,
            'linkToTop' => 0,
            'file_collections' => '',
            'filelink_size' => 0,
            'filelink_sorting' => '',
            'target' => '',
            'section_frame' => 0,
            'date' => 0,
            'multimedia' => '',
            'image_frames' => 0,
            'recursive' => 0,
            'imageheight' => 0,
            'rte_enabled' => 0,
            'sys_language_uid' => 1,
            'tx_impexp_origuid' => 0,
            'pi_flexform' => '',
            'accessibility_title' => '',
            'accessibility_bypass' => 0,
            'accessibility_bypass_text' => '',
            'l18n_parent' => 0,
            'selected_categories' => '',
            'category_field' => '',
            'categories' => 0,
        );

        $this->dataHandlerTranslatedUpdatedContent->datamap = array (

            'tt_content' => array (
                '88' => array (
                    'CType' => 'text',
                    'colPos' => 0,
                    'sys_language_uid' => 1,
                    'l18n_parent' => 0,
                    'header' => 'English Content',
                    'header_layout' => 0,
                    'header_position' => '',
                    'date' => 0,
                    'header_link' => '',
                    '_TRANSFORM_bodytext' => 'RTE',
                    'bodytext' => '<p>English Content</p>',
                    'rte_enabled' => 0,
                    'layout' => 0,
                    'spaceBefore' => 0,
                    'spaceAfter' => 0,
                    'section_frame' => 0,
                    'hidden' => 0,
                    'sectionIndex' => 1,
                    'linkToTop' => 0,
                    'fe_group' => '',
                    'categories' => '',
                )
            )
        );

        //=================================================
        /*
         * Moved content with uid = 88 from page with uid = 111 to page with uid = 102
         */
        //=================================================
        $this->dataHandlerTranslatedMovedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedMovedContent->substNEWwithIDs = array ();
        $this->dataHandlerTranslatedMovedContent->cmdmap = array ();

        $this->dataHandlerTranslatedMovedContent->checkValue_currentRecord = array (
            'uid' => 89,
            'pid' => 102,
            't3ver_oid' => 0,
            't3ver_id' => 0,
            't3ver_wsid' => 0,
            't3ver_label' => '',
            't3ver_state' => 0,
            't3ver_stage' => 0,
            't3ver_count' => 0,
            't3ver_tstamp' => 0,
            't3ver_move_id' => 0,
            't3_origuid' => 0,
            'tstamp' => 1423551543,
            'crdate' => 1423550415,
            'cruser_id' => 1,
            'hidden' => 0,
            'sorting' => 8,
            'CType' => 'text',
            'header' => 'Testheadlines',
            'header_position' => '',
            'bodytext' => 'Testtext',
            'image' => '',
            'imagewidth' => 0,
            'imageorient' => 0,
            'imagecaption' => '',
            'imagecols' => 2,
            'imageborder' => 0,
            'media' => '',
            'layout' => 0,
            'deleted' => 0,
            'cols' => 0,
            'records' => '',
            'pages' => '',
            'starttime' => 0,
            'endtime' => 0,
            'colPos' => 0,
            'subheader' => '',
            'spaceBefore' => 0,
            'spaceAfter' => 0,
            'fe_group' => '',
            'header_link' => '',
            'imagecaption_position' => '',
            'image_link' => '',
            'image_zoom' => 0,
            'image_noRows' => 0,
            'image_effects' => 0,
            'image_compression' => 0,
            'altText' => '',
            'titleText' => '',
            'longdescURL' => '',
            'header_layout' => 0,
            'menu_type' => 0,
            'list_type' => '',
            'table_border' => 0,
            'table_cellspacing' => 0,
            'table_cellpadding' => 0,
            'table_bgColor' => 0,
            'select_key' => '',
            'sectionIndex' => 1,
            'linkToTop' => 0,
            'file_collections' => '',
            'filelink_size' => 0,
            'filelink_sorting' => '',
            'target' => '',
            'section_frame' => 0,
            'date' => 0,
            'multimedia' => '',
            'image_frames' => 0,
            'recursive' => 0,
            'imageheight' => 0,
            'rte_enabled' => 0,
            'sys_language_uid' => 1,
            'pi_flexform' => '',
            'accessibility_title' => '',
            'accessibility_bypass' => 0,
            'accessibility_bypass_text' => '',
            'l18n_parent' => 0,
            'selected_categories' => '',
            'category_field' => '',
            'categories' => 0,
        );


        $this->dataHandlerTranslatedMovedContent->datamap = array (
            'tt_content' => array (
                '89' => array (
                    'colPos' => 0,
                    'sys_language_uid' => 1,
                )
            )
        );

        //=================================================
        /*
         * Swapped content with uid = 87 to uid = 84 on page uid = 111
         */
        //=================================================
        $this->dataHandlerTranslatedSwappedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedSwappedContent->substNEWwithIDs = array ();
        $this->dataHandlerTranslatedSwappedContent->cmdmap = array (

            'tt_content' => array (
                '88' => array (
                    'version' => array (
                        'action' => 'swap',
                        'swapWith' => 89,
                        'comment' => '',
                        'notificationAlternativeRecipients' => array ()
                    )
                )
            )

        );

        $this->dataHandlerTranslatedSwappedContent->checkValue_currentRecord = array ();
        $this->dataHandlerTranslatedSwappedContent->datamap = array ();

        //=================================================
        /*
         * Deleted content with uid = 88 on page uid = 111
         */
        //=================================================
        $this->dataHandlerTranslatedDeletedContent = new \TYPO3\CMS\Core\DataHandling\DataHandler();
        $this->dataHandlerTranslatedDeletedContent->substNEWwithIDs = array ();
        $this->dataHandlerTranslatedDeletedContent->cmdmap = array (

            'tt_content' => array (
                '88' => array (
                    'delete' => 1
                )
            )

        );
        $this->dataHandlerTranslatedDeletedContent->checkValue_currentRecord = array ();
        $this->dataHandlerTranslatedDeletedContent->datamap = array ();


    }

    /**
     *  Tear down fixture
     */
    public function tearDown() {
        try {
            if ($this->fixture->hasRepository())
                $this->fixture->getRepository()->removeAll();
        } catch (\RKW\RkwSearch\Exception $e) {
            // do nothing
        }
        unset($this->fixture);
        unset($this->dataHandlerDefault);
    }


    //###############################################################

    /**
     * @test
     */
    public function initGivenDefaultNewPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('new', 'pages', 'NEW54d8dc268badf' , $this->dataHandlerDefaultNewPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(111, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultUpdatedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('update', 'pages', 111 , $this->dataHandlerDefaultUpdatedPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(111, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultMovedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('move', 'pages', 111 , $this->dataHandlerDefaultMovedPage, 1);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(111, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultSwappedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('swap', 'pages', 111 , $this->dataHandlerDefaultSwappedPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(111, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultDeletedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('swap', 'pages', 111 , $this->dataHandlerDefaultDeletedPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(111, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }


    /**
     * @test
     */
    public function initGivenDefaultNewContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('new', 'tt_content', 'NEW54d9a7c52bdb7' , $this->dataHandlerDefaultNewContent);

        $record = $this->fixture->getRecord();


        $object = $this->fixture->getObject();


        // check if uid and language fits the expected data
        $this->assertSame(84, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultUpdatedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('update', 'tt_content', 84 , $this->dataHandlerDefaultUpdatedContent);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(84, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultMovedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('move', 'tt_content', 83 , $this->dataHandlerDefaultMovedContent, 102);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(83, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(102, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultSwappedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('swap', 'tt_content', 84 , $this->dataHandlerDefaultSwappedContent);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(84, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenDefaultDeletedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('delete', 'tt_content', 84 , $this->dataHandlerDefaultDeletedContent);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(84, $record['uid']);
        $this->assertSame(NULL, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }



    /**
     * @test
     */
    public function initGivenTranslatedNewPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('new', 'pages_language_overlay', 'NEW54d8e0c85c859' , $this->dataHandlerTranslatedNewPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(8, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(8, $object->getUid());
        $this->assertSame(1, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenTranslatedUpdatedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('update', 'pages_language_overlay', 8 , $this->dataHandlerTranslatedUpdatedPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(8, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(8, $object->getUid());
        $this->assertSame(1, $object->getLanguageUid());

    }


    /**
     * @test
     */
    public function initGivenTranslatedSwappedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('swap', 'pages_language_overlay', 8 , $this->dataHandlerTranslatedSwappedPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(8, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(8, $object->getUid());
        $this->assertSame(1, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenTranslatedDeletedPageSetsCorrespondingRecordAndObject() {

        $this->fixture->init('delete', 'pages_language_overlay', 8 , $this->dataHandlerDefaultDeletedPage);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(8, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(8, $object->getUid());
        $this->assertSame(1, $object->getLanguageUid());

    }


    /**
     * @test
     */
    public function initGivenTranslatedNewContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('new', 'tt_content', 'NEW54d9bd6a95257' , $this->dataHandlerTranslatedNewContent);

        $record = $this->fixture->getRecord();


        $object = $this->fixture->getObject();


        // check if uid and language fits the expected data
        $this->assertSame(88, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenTranslatedUpdatedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('update', 'tt_content', 88 , $this->dataHandlerTranslatedUpdatedContent);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(88, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenTranslatedMovedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('move', 'tt_content', 89 , $this->dataHandlerTranslatedMovedContent, 102);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(89, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(102, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenTranslatedSwappedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('swap', 'tt_content', 88 , $this->dataHandlerTranslatedSwappedContent);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(88, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    /**
     * @test
     */
    public function initGivenTranslatedDeletedContentSetsCorrespondingRecordAndObject() {

        $this->fixture->init('delete', 'tt_content', 88 , $this->dataHandlerTranslatedDeletedContent);

        $record = $this->fixture->getRecord();
        $object = $this->fixture->getObject();

        // check if uid and language fits the expected data
        $this->assertSame(88, $record['uid']);
        $this->assertSame(1, $record['sysLanguageUid']);

        $this->assertSame(111, $object->getUid());
        $this->assertSame(0, $object->getLanguageUid());

    }

    //###############################################################

    /**
     * @test
     */
    public function importSubGivenInvalidObjectReturnsZero() {

        $this->fixture->init('new', 'pages', 111 , $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame(0, $this->fixture->importSub(new \TYPO3\CMS\Core\DataHandling\DataHandler()));

    }

    /**
     * @test
     */
    public function importSubGivenEmptyInstanceOfModelInterfaceReturnsZero() {

        $this->fixture->init('update', 'pages', 111 , $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame(0, $this->fixture->importSub($this->fixture->getModel()));

    }

    /**
     * @test
     */
    public function importSubGivenInstanceOfModelInterfaceReturnsOne() {

        $this->fixture->init('update', 'pages', 111 , $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame(1, $this->fixture->importSub($this->fixture->getObject()));

    }

    //###############################################################

    /**
     * @test
     */
    public function prepareImportGivenInvalidObjectReturnsNull() {

        $this->fixture->init('update', 'phantasia', 815 , new \TYPO3\CMS\Core\DataHandling\DataHandler());
        $this->assertNull($this->fixture->prepareImport());


    }

    /**
     * @test
     */
    public function prepareImportReturnsInstanceOfModelInterface() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $this->fixture->prepareImport());


    }

    /**
     * @test
     */
    public function prepareImportGivenDefaultNewPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('new', 'pages', 111, $this->dataHandlerDefaultNewPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());

    }

    /**
     * @test
     */
    public function prepareImportGivenDefaultUpdatedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());
    }

    /**
     * @test
     */
    public function prepareImportGivenDefaultMovedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('move', 'pages', 111, $this->dataHandlerDefaultMovedPage, 1);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());

    }

    /**
     * @test
     */
    public function prepareImportGivenDefaultSwappedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('swap', 'pages', 111, $this->dataHandlerDefaultSwappedPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());
    }

    /**
     * @test
     */
    public function prepareImportGivenDefaultDeletedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('delete', 'pages', 111, $this->dataHandlerDefaultDeletedPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());
    }



    /**
     * @test
     */
    public function prepareImportGivenTranslatedNewPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('new', 'pages_language_overlay', 8, $this->dataHandlerTranslatedNewPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-1. Test B-111-1', $result->getContent());

    }

    /**
     * @test
     */
    public function prepareImportGivenTranslatedUpdatedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-1. Test B-111-1', $result->getContent());
    }


    /**
     * @test
     */
    public function prepareImportGivenTranslatedSwappedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('swap', 'pages_language_overlay', 8, $this->dataHandlerTranslatedSwappedPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-1. Test B-111-1', $result->getContent());
    }

    /**
     * @test
     */
    public function prepareImportGivenTranslatedDeletedPageReturnsInstanceOfModelInterfaceWithAddedContentData() {

        $this->fixture->init('delete', 'pages_language_overlay', 8, $this->dataHandlerTranslatedDeletedPage);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);
        $this->assertSame('Test A-111-1. Test B-111-1', $result->getContent());
    }


    /**
     * @test
     */
    public function prepareImportGivenDefaultNewContentReturnsInstanceOfModelInterfaceWithExpectedProperties() {

        $this->fixture->init('new', 'tt_content', 'NEW54d9a7c52bdb7', $this->dataHandlerDefaultNewContent);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);

        // data of page uid = 111
        $this->assertSame(111, $result->getUid());
        $this->assertSame(1, $result->getPid());
        $this->assertSame(0, $result->getLanguageUid());
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());

    }

    /**
     * @test
     */
    public function prepareImportGivenDefaultUpdatedContentReturnsInstanceOfModelInterfaceWithExpectedProperties() {

        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);

        // data of page uid = 111
        $this->assertSame(111, $result->getUid());
        $this->assertSame(1, $result->getPid());
        $this->assertSame(0, $result->getLanguageUid());
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());

    }


    /**
     * @test
     */
    public function prepareImportGivenDefaultMovedContentReturnsInstanceOfModelInterfaceWithExpectedProperties() {

        $this->fixture->init('moved', 'tt_content', 83, $this->dataHandlerDefaultMovedContent, 102);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);

        // data of page uid = 102
        $this->assertSame(102, $result->getUid());
        $this->assertSame(1, $result->getPid());
        $this->assertSame(0, $result->getLanguageUid());
        $this->assertSame('Test A-102-0. Test B-102-0', $result->getContent());

    }


    /**
     * @test
     */
    public function prepareImportGivenDefaultSwappedContentReturnsInstanceOfModelInterfaceWithExpectedProperties() {

        $this->fixture->init('swap', 'tt_content', 84, $this->dataHandlerDefaultSwappedContent);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);

        // data of page uid = 111
        $this->assertSame(111, $result->getUid());
        $this->assertSame(1, $result->getPid());
        $this->assertSame(0, $result->getLanguageUid());
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());

    }


    /**
     * @test
     */
    public function prepareImportGivenDefaultDeletedContentReturnsInstanceOfModelInterfaceWithExpectedProperties() {

        $this->fixture->init('swap', 'tt_content', 84, $this->dataHandlerDefaultDeletedContent);
        $result = $this->fixture->prepareImport();
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $result);

        // data of page uid = 111
        $this->assertSame(111, $result->getUid());
        $this->assertSame(1, $result->getPid());
        $this->assertSame(0, $result->getLanguageUid());
        $this->assertSame('Test A-111-0. Test B-111-0', $result->getContent());

    }


    
    //###############################################################


    /**
     * @test
     */
    public function prepareImportContentGivenNoOldUidReturnsEmptyArray() {

        $this->fixture->init('move', 'pages', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertInternalType('array', $this->fixture->prepareImportContent());
        $this->assertEmpty($this->fixture->prepareImportContent());

    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultMovedPageReturnsEmptyArray() {

        // moving page 111 from under page 1 to under page 99
        $this->fixture->init('move', 'pages', 111, $this->dataHandlerDefaultMovedPage, 1);
        $this->assertEmpty($this->fixture->prepareImportContent());

    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultNewContentReturnsArrayWithInstancesOfModelInterface() {

        // inserting tt_content 84
        $this->fixture->init('new', 'tt_content', 'NEW54d9a7c52bdb7', $this->dataHandlerDefaultNewContent);
        foreach ($this->fixture->prepareImportContent() as $record)
            $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $record);

    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultNewContentReturnsArrayWithInstancesWithExpectedProperties() {

        // inserting tt_content 84
        $this->fixture->init('new', 'tt_content', 'NEW54d9a7c52bdb7', $this->dataHandlerDefaultNewContent);
        $dataArray = $this->fixture->prepareImportContent();

        // translation overlay of page 102
        $this->assertSame(8, $dataArray[0]->getUid());
        $this->assertSame(111, $dataArray[0]->getPid());
        $this->assertSame(1, $dataArray[0]->getLanguageUid());
        $this->assertSame('Test A-111-1. Test B-111-1', $dataArray[0]->getContent());


    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultUpdatedContentReturnsArrayWithInstancesOfModelInterface() {

        // inserting tt_content 84
        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        foreach ($this->fixture->prepareImportContent() as $record)
            $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $record);

    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultUpdatedContentReturnsArrayWithInstancesWithExpectedProperties() {

        // inserting tt_content 84
        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);

        $dataArray = $this->fixture->prepareImportContent();

        // translation overlay of page 102
        $this->assertSame(8, $dataArray[0]->getUid());
        $this->assertSame(111, $dataArray[0]->getPid());
        $this->assertSame(1, $dataArray[0]->getLanguageUid());
        $this->assertSame('Test A-111-1. Test B-111-1', $dataArray[0]->getContent());

    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultMovedContentReturnsArrayWithInstancesOfModelInterface() {

        // moving tt_content 83 from page 1 to page 102
        $this->fixture->init('move', 'tt_content', 83, $this->dataHandlerDefaultMovedContent, 1);
        foreach ($this->fixture->prepareImportContent() as $record)
            $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $record);

    }

    /**
     * @test
     */
    public function prepareImportContentGivenDefaultMovedContentReturnsArrayWithInstancesWithExpectedProperties() {

        // moving tt_content 83 from page 1 to page 102
        $this->fixture->init('move', 'tt_content', 83, $this->dataHandlerDefaultMovedContent, 1);

        $dataArray = $this->fixture->prepareImportContent();



        // old page, default language
        $this->assertSame(1, $dataArray[0]->getUid());
        $this->assertSame(0, $dataArray[0]->getPid());
        $this->assertSame(0, $dataArray[0]->getLanguageUid());
        $this->assertSame('Test A-1-0. Test B-1-0', $dataArray[0]->getContent());


        // translation overlay of page 1
        $this->assertSame(10, $dataArray[1]->getUid());
        $this->assertSame(1, $dataArray[1]->getPid());
        $this->assertSame(1, $dataArray[1]->getLanguageUid());
        $this->assertSame('Test A-1-1. Test B-1-1', $dataArray[1]->getContent());

        // translation overlay of page 102
        $this->assertSame(8, $dataArray[2]->getUid());
        $this->assertSame(102, $dataArray[2]->getPid());
        $this->assertSame(1, $dataArray[2]->getLanguageUid());
        $this->assertSame('Test A-102-1. Test B-102-1', $dataArray[2]->getContent());

    }

    /**
     * @test
     */
    public function prepareImportContentGivenTranslatedNewContentReturnsArrayWithInstancesOfModelInterface() {

        // inserting tt_content 88
        $this->fixture->init('new', 'tt_content', 'NEW54d9bd6a95257', $this->dataHandlerTranslatedNewContent);
        foreach ($this->fixture->prepareImportContent() as $record)
            $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $record);

    }

    /**
     * @test
     */
    public function prepareImportContentGivenTranslatedNewContentReturnsArrayWithInstancesWithExpectedProperties() {

        // inserting tt_content 88
        $this->fixture->init('new', 'tt_content', 'NEW54d9bd6a95257', $this->dataHandlerTranslatedNewContent);
        $dataArray = $this->fixture->prepareImportContent();

        // translation overlay of page 102
        $this->assertSame(8, $dataArray[0]->getUid());
        $this->assertSame(111, $dataArray[0]->getPid());
        $this->assertSame(1, $dataArray[0]->getLanguageUid());
        $this->assertSame('Test A-111-1. Test B-111-1', $dataArray[0]->getContent());

    }

    /**
     * @test
     */
    public function prepareImportContentGivenTranslatedUpdatedContentReturnsArrayWithInstancesOfModelInterface() {

        // inserting tt_content 88
        $this->fixture->init('update', 'tt_content', 88, $this->dataHandlerTranslatedUpdatedContent);
        foreach ($this->fixture->prepareImportContent() as $record)
            $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $record);

    }

    /**
     * @test
     */
    public function prepareImportContentGivenTranslatedUpdatedContentReturnsArrayWithInstancesWithExpectedProperties() {

        // inserting tt_content 84
        $this->fixture->init('update', 'tt_content', 88, $this->dataHandlerTranslatedUpdatedContent);
        $dataArray = $this->fixture->prepareImportContent();

        // translation overlay of page 102
        $this->assertSame(8, $dataArray[0]->getUid());
        $this->assertSame(111, $dataArray[0]->getPid());
        $this->assertSame(1, $dataArray[0]->getLanguageUid());
        $this->assertSame('Test A-111-1. Test B-111-1', $dataArray[0]->getContent());

    }

    /**
     * @test
     */
    public function prepareImportContentGivenTranslatedMovedContentReturnsArrayWithInstancesOfModelInterface() {

        // inserting tt_content 89
        $this->fixture->init('move', 'tt_content', 89, $this->dataHandlerTranslatedMovedContent);
        foreach ($this->fixture->prepareImportContent() as $record)
            $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $record);

    }


    /**
     * @test
     */
    public function prepareImportContentGivenTranslatedMovedContentReturnsArrayWithInstancesWithExpectedProperties() {

        // moving tt_content 89 from page 1 to page 102
        $this->fixture->init('move', 'tt_content', 89, $this->dataHandlerTranslatedMovedContent, 1);

        $dataArray = $this->fixture->prepareImportContent();

        // old page, default language
        $this->assertSame(1, $dataArray[0]->getUid());
        $this->assertSame(0, $dataArray[0]->getPid());
        $this->assertSame(0, $dataArray[0]->getLanguageUid());
        $this->assertSame('Test A-1-0. Test B-1-0', $dataArray[0]->getContent());

        // translation overlay of page 1
        $this->assertSame(10, $dataArray[1]->getUid());
        $this->assertSame(1, $dataArray[1]->getPid());
        $this->assertSame(1, $dataArray[1]->getLanguageUid());
        $this->assertSame('Test A-1-1. Test B-1-1', $dataArray[1]->getContent());

        // translation overlay of page 102
        $this->assertSame(8, $dataArray[2]->getUid());
        $this->assertSame(102, $dataArray[2]->getPid());
        $this->assertSame(1, $dataArray[2]->getLanguageUid());
        $this->assertSame('Test A-102-1. Test B-102-1', $dataArray[2]->getContent());


    }

    //###############################################################


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getUidGivenInvalidUidThrowsException() {

        $this->fixture->init('new', 'pages', 'test', $this->dataHandlerDefaultNewPage);
        $this->assertSame(111, $this->fixture->getUid());
    }

    /**
     * @test
     */
    public function getUidGivenNewDataObjectReturnsUid() {

        $this->fixture->init('new', 'pages', 'NEW54d8dc268badf', $this->dataHandlerDefaultNewPage);
        $this->assertSame(111, $this->fixture->getUid());
    }

    /**
     * @test
     */
    public function getUidGivenNormalDataObjectReturnsUid() {

        $this->fixture->init('update', 'page', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame(111, $this->fixture->getUid());
    }


    //###############################################################

    /**
     * @test
     */
    public function getOldUidGivenNoDataReturnsNull() {

        $this->fixture->init('move', 'pages', 111, $this->dataHandlerDefaultMovedPage);
        $this->assertNull($this->fixture->getOldUid());
    }

    /**
     * @test
     */
    public function getOldUidReturnsOldUid() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage, 18);
        $this->assertSame(18, $this->fixture->getOldUid());
    }


    //###############################################################

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getActionWithoutInitThrowsException() {

        $this->fixture->getAction();
    }


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getActionWithEmptyActionThrowsException() {

        $this->fixture->init('', 'pages', 111, $this->dataHandlerDefaultUpdatedContent);
        $this->fixture->getAction();
    }

    /**
     * @test
     */
    public function getActionReturnsInitAction() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('update', $this->fixture->getAction());
    }

    /**
     * @test
     */
    public function getActionGivenTtContentTableAndNewActionReturnsUpdateAction() {

        $this->fixture->init('new', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('update', $this->fixture->getAction());
    }


    /**
     * @test
     */
    public function getActionGivenTtContentTableAndSwapActionReturnsUpdateAction() {

        $this->fixture->init('swap', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('update', $this->fixture->getAction());
    }

    /**
     * @test
     */
    public function getActionGivenTtContentTableAndNewActionAndSwitchFalseReturnsInitAction() {

        $this->fixture->init('new', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('new', $this->fixture->getAction(FALSE));
    }


    /**
     * @test
     */
    public function getActionGivenTtContentTableAndSwapActionAndSwitchFalseReturnsInitAction() {

        $this->fixture->init('swap', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('swap', $this->fixture->getAction(FALSE));
    }


    //###############################################################

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getTableWithoutInitThrowsException() {

        $this->fixture->getTable();
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getTableGivenEmptyTableThrowsException() {

        $this->fixture->init('update', '', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->fixture->getTable();
    }

    /**
     * @test
     */
    public function getTableReturnsInitTable() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame('pages', $this->fixture->getTable());
    }

    /**
     * @test
     */
    public function getTableGivenPagesLanguageOverlayTableReturnsPagesLanguageOverlayTable() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertSame('pages_language_overlay', $this->fixture->getTable());
    }

    /**
     * @test
     */
    public function getTableGivenTtContentTableReturnsPagesTable() {

        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('pages', $this->fixture->getTable());
    }


    //###############################################################
    /**
     * @test
     */
    public function getTableOverlayGivenTtContentReturnsPagesLanguageOverlayTable() {

        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('pages_language_overlay',$this->fixture->getTableOverlay());
    }

    /**
     * @test
     */
    public function getTableOverlayGivenPagesLanguageOverlayTableReturnsPagesLanguageOverlayTable() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertSame('pages_language_overlay', $this->fixture->getTableOverlay());
    }

    /**
     * @test
     */
    public function getTableOverlayGivenPagesTableReturnsPagesLanguageOverlayTable() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame('pages_language_overlay', $this->fixture->getTableOverlay());
    }


    //###############################################################

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getTableRawWithoutInitThrowsException() {

        $this->fixture->getTableRaw();
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getTableRawGivenEmptyTableThrowsException() {

        $this->fixture->init('update', '', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->fixture->getTableRaw();
    }

    /**
     * @test
     */
    public function getTableRawReturnsInitTable() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame('pages', $this->fixture->getTableRaw());
    }

    /**
     * @test
     */
    public function getTableRawGivenPagesLanguageOverlayTableReturnsPagesLanguageOverlayTable() {

        $this->fixture->init('update', 'pages_language_overlay', 111, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertSame('pages_language_overlay', $this->fixture->getTableRaw());
    }

    /**
     * @test
     */
    public function getTableRawGivenTtContentTableReturnsTtContentTable() {

        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('tt_content', $this->fixture->getTableRaw());
    }



    //###############################################################
    /**
     * @test
     */
    public function getTableBaseGivenTtContentReturnsPagesTable() {

        $this->fixture->init('update', 'tt_content', 84, $this->dataHandlerDefaultUpdatedContent);
        $this->assertSame('pages',$this->fixture->getTableBase());
    }

    /**
     * @test
     */
    public function getTableBaseGivenPagesLanguageOverlayTableReturnsPagesTable() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertSame('pages', $this->fixture->getTableBase());
    }

    /**
     * @test
     */
    public function getTableOverlayGivenPagesTableReturnsPagesTable() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame('pages', $this->fixture->getTableBase());
    }
    //###############################################################


    /**
     * @test
     */
    public function isTableOverlayGivenPagesTableReturnsFalse() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertFalse($this->fixture->isTableOverlay());
    }

    /**
     * @test
     */
    public function isTableOverlayGivenPagesLanguageOverlayTableReturnsTrue() {

        $this->fixture->init('update', 'pages_language_overlay', 111, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertTrue($this->fixture->isTableOverlay());
    }

    //###############################################################

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getRecordWithoutInitThrowsException() {

        $this->fixture->getRecord();
    }


    /**
     * @test
     */
    public function getRecordReturnsInstanceArray() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInternalType('array', $this->fixture->getRecord());

    }

    //###############################################################

    /**
     * @test
     */
    public function hasRecordWithoutInitReturnsFalse() {

        $this->assertFalse($this->fixture->hasRecord());
    }

    /**
     * @test
     */
    public function hasRecordWithInvalidDataReturnsFalse() {

        $this->fixture->init('update', 'pages', 815 , new \TYPO3\CMS\Core\DataHandling\DataHandler());
        $this->assertFalse($this->fixture->hasRecord());
    }

    /**
     * @test
     */
    public function hasRecordReturnsTrue() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertTrue($this->fixture->hasRecord());
    }


    //###############################################################

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getObjectWithoutInitThrowsException() {

        $this->fixture->getObject();
    }


    /**
     * @test
     */
    public function getObjectReturnsInstanceOfModelInterface() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $this->fixture->getObject());

    }

    //###############################################################

    /**
     * @test
     */
    public function hasObjectWithoutInitReturnsFalse() {

        $this->assertFalse($this->fixture->hasObject());
    }

    /**
     * @test
     */
    public function hasObjectWithInvalidDataReturnsFalse() {

        $this->fixture->init('update', 'phantasia', 815 , new \TYPO3\CMS\Core\DataHandling\DataHandler());
        $this->assertFalse($this->fixture->hasObject());
    }

    /**
     * @test
     */
    public function hasObjectReturnsTrue() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertTrue($this->fixture->hasObject());
    }


    //###############################################################

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getDatabaseHandlerWithoutInitThrowsException() {

        $this->fixture->getDatabaseHandler();
    }

    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getDatabaseHandlerGivenInvalidObjectThrowsException() {

        $this->fixture->init('update', 'pages', 111, array ());
        $this->fixture->getDatabaseHandler();
    }

    /**
     * @test
     */
    public function getDatabaseHandlerReturnsInstanceOfDataHandler() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInstanceOf('TYPO3\\CMS\\Core\\DataHandling\\DataHandler', $this->fixture->getDatabaseHandler());
    }


    //######################################################################################

    /**
     * @test
     */
    public function getRepositoryGivenInvalidTableReturnsNull() {

        $this->fixture->init('update', 'test', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertNull($this->fixture->getRepository());

    }

    /**
     * @test
     */
    public function getRepositoryGivenPagesTableReturnsInstanceOfRepositoryInterface() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\RepositoryInterface', $this->fixture->getRepository());

    }

    /**
     * @test
     */
    public function getRepositoryGivenPagesLanguageOverlayTableReturnsInstanceOfRepositoryInterface() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Repository\\RepositoryInterface', $this->fixture->getRepository());

    }

    //######################################################################################

    /**
     * @test
     */
    public function hasRepositoryGivenInvalidTableReturnsNull() {

        $this->fixture->init('update', 'test', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertNull($this->fixture->hasRepository('test'));

    }

    /**
     * @test
     */
    public function hasRepositoryGivenPagesTableReturnsString() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInternalType('string', $this->fixture->hasRepository());

    }

    /**
     * @test
     */
    public function hasRepositoryGivenPagesLanguageOverlayTableReturnsString() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertInternalType('string', $this->fixture->hasRepository());

    }


    //######################################################################################

    /**
     * @test
     */
    public function getModelGivenInvalidTableReturnsNull() {

        $this->fixture->init('update', 'test', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertNull($this->fixture->getModel());

    }

    /**
     * @test
     */
    public function getModelGivenPagesTableReturnsInstanceOfModelInterface() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $this->fixture->getModel());

    }

    /**
     * @test
     */
    public function getModelGivenPagesLanguageOverlayTableReturnsInstanceOfModelInterface() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertInstanceOf('RKW\\RkwSearch\\OrientDb\\Domain\\Model\\ModelInterface', $this->fixture->getModel());

    }

    //######################################################################################

    /**
     * @test
     */
    public function hasModelGivenInvalidTableReturnsNull() {

        $this->fixture->init('update', 'test', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertNull($this->fixture->hasModel());

    }

    /**
     * @test
     */
    public function hasModelGivenPagesTableReturnsString() {

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertInternalType('string', $this->fixture->hasModel());

    }

    /**
     * @test
     */
    public function hasModelGivenPagesLanguageOverlayTableReturnsString() {

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertInternalType('string', $this->fixture->hasModel());

    }

    //######################################################################################


    /**
     * @test
     * @expectedException \RKW\RkwSearch\Exception
     */
    public function getQueryFieldsGivenEmptyTableThrowsException() {

        $this->fixture->getQueryFields('');

    }

    /**
     * @test
     */
    public function getQueryFieldsGivenPagesTableReturnsMappedDefaultFields() {

        $testArray = array (
            0 => 'uid',
            1 => 'pid',
            2 => 'tstamp AS tstamp',
            3 => 'crdate AS crdate',
            4 => 'sorting AS sorting',
            5 => 'hidden AS hidden',
            6 => 'tx_rkwsearch_no_search AS noSearch',
            7 => 't3ver_oid',
            8 => 't3ver_state',
            9 => 'doktype AS doktype',
            10 => 'title AS title',
            11 => 'subtitle AS subtitle',
            12 => 'tx_rkwbasics_teaser_text AS abstract',
            13 => 'description AS description',
            14 => 'keywords AS keywords',
            15 => 'tx_bmpdf2content_is_import AS pdfImport',
            16 => 'tx_bmpdf2content_is_import_sub AS pdfImportSub',
            17 => 'tx_rkwsearch_pubdate AS pubdate',
        );

        $this->fixture->init('update', 'pages', 111, $this->dataHandlerDefaultUpdatedPage);
        $this->assertSame( $testArray, $this->fixture->getQueryFields('pages'));

    }


    /**
     * @test
     */
    public function getQueryFieldsGivenPagesLanguageOverlayTableReturnsMappedOverlayFields() {

        $testArray = array (
            0 => 'uid',
            1 => 'pid',
            2 => 'tstamp AS tstamp',
            3 => 'crdate AS crdate',
            4 => 'sys_language_uid AS sysLanguageUid',
            5 => 'hidden AS hidden',
            6 => 'tx_rkwsearch_no_search AS noSearch',
            7 => 't3ver_oid',
            8 => 't3ver_state',
            9 => 'doktype AS doktype',
            10 => 'title AS title',
            11 => 'subtitle AS subtitle',
            12 => 'tx_rkwbasics_teaser_text AS abstract',
            13 => 'description AS description',
            14 => 'keywords AS keywords',
        );

        $this->fixture->init('update', 'pages_language_overlay', 8, $this->dataHandlerTranslatedUpdatedPage);
        $this->assertSame( $testArray, $this->fixture->getQueryFields('pages_language_overlay'));

    }

} 