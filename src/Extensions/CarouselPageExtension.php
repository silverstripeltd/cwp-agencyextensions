<?php

namespace CWP\AgencyExtensions\Extensions;

use CWP\AgencyExtensions\Model\CarouselItem;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TextField;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\HasManyList;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * @method HasManyList<CarouselItem> CarouselItems()
 *
 * @extends Extension<SiteTree>
 */
class CarouselPageExtension extends Extension
{
    private static array $db = [
        'CarouselTitle' => 'Text',
    ];

    private static array $has_many = [
        'CarouselItems' => CarouselItem::class,
    ];

    private static array $owns = [
        'CarouselItems',
    ];

    /**
     * @return DataList<CarouselItem>
     */
    public function getCarouselItems(): DataList
    {
        return $this->getOwner()->getComponents('CarouselItems')->sort('SortOrder');
    }

    /**
     * Add the carousel management GridField to the Page's CMS fields
     *
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields): void
    {
        $gridField = GridField::create(
            'CarouselItems',
            _t(__CLASS__ . '.TITLE', 'Hero/Carousel'),
            $this->getCarouselItems(),
            GridFieldConfig_RelationEditor::create()
        );
        $gridField->setDescription(_t(
            __CLASS__ . '.NOTE',
            'Carousel functionality will automatically be loaded when 2 or more items are added below'
        ));
        $gridConfig = $gridField->getConfig();
        $gridConfig->getComponentByType(GridFieldAddNewButton::class)->setButtonName(
            _t(__CLASS__ . '.ADDNEW', 'Add new')
        );
        $gridConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
        $gridConfig->removeComponentsByType(GridFieldDeleteAction::class);
        $gridConfig->addComponent(new GridFieldDeleteAction());
        $gridConfig->addComponent(new GridFieldOrderableRows('SortOrder'));
        $gridConfig->removeComponentsByType(GridFieldSortableHeader::class);
        $gridField->setModelClass(CarouselItem::class);

        $fields->findOrMakeTab(
            'Root.Carousel',
            _t(__CLASS__ . '.TITLE', 'Hero/Carousel')
        );

        $titleField = TextField::create('CarouselTitle', 'Carousel Title');
        $titleField->setDescription(_t(
            __CLASS__ . '.TITLE_NOTE',
            'Used by screen readers'
        ));

        $fields->addFieldToTab('Root.Carousel', $titleField);
        $fields->addFieldToTab('Root.Carousel', $gridField);
    }
}
