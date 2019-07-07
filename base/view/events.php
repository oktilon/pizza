<?php

    if(!$this->assertRead()) {
        $this->redirectToLoginOrAccess();
    }

    if($this->args) {
        $this->action = array_shift($this->args);
        $this->runPhpScript();
        return;
    }

    $empty_item = new Event();
    $empty_item = $empty_item->getJson(false, false);
    foreach ($empty_item->name as $key => $val) {
        $nk = "name_{$key}";
        $dk = "desc_{$key}";
        $empty_item->$nk = '';
        $empty_item->$dk = '';
    }
    $empty_item->plc = $empty_item->plc->id;
    $empty_item->owner = $empty_item->owner->id;
    unset($empty_item->name, $empty_item->desc);
    $this->addJsVar('empty_item', $empty_item);


    $this->addCss('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css');
    $this->addJScript('/vendor/bootstrap-datetimepicker/bootstrap-datetimepicker.js');

    $this->set('title', _('Events'));
    $this->set('event', _('Event'));
    $this->set('name',  _('Name'));
    $this->set('save',  _('Save'));
    $this->set('close', _('Close'));
    $this->set('date',  _('Date'));
    $this->set('owner', _('Owner'));
    $this->set('place', _('Place'));
    $this->set('alias', _('Alias'));
    $this->set('description',      _('Description'));
    $this->set('name_len5_error',  _('Name length should be 5 characters minimum'));
    $this->set('name_req_error',   _('Name is required'));
    $this->set('date_req_error',   _('Date is required'));
    $this->set('desc_len10_error', _('Description length should be 10 characters minimum'));


    $languages = [];
    foreach (PageManager::$languages as $lng) $languages[] = $lng;
    $this->addJsVar('languages', $languages);
    $this->addJsVar('owners', CFirm::getList(['json']));
    $this->addJsVar('places', Place::getList(['json']));

    // By language
    $names = '';
    $descTabs = '';
    $descPans = '';
    $first = true;
    $this->prepareTemplate('event_name');
    $this->prepareTemplate('event_desc_tab');
    $this->prepareTemplate('event_desc_panel');
    foreach (PageManager::$languages as $id => $l) {
        $lng = $l->getJson();
        $lng->act = $first ? ' active' : '';
        $lng->act_pan = $first ? ' show active' : '';
        $lng->sel = $first ? ' true' : 'false';
        $names .= $this->applyTemplate('event_name', $lng);
        $descTabs .= $this->applyTemplate('event_desc_tab', $lng);
        $descPans .= $this->applyTemplate('event_desc_panel', $lng);
        $first = false;
    }
    $this->set('names',  $names);
    $this->set('desc_tabs',  $descTabs);
    $this->set('desc_panels',  $descPans);

    //PageManager::debug(PageManager::$languages, 'languages');