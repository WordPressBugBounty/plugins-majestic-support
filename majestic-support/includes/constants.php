<?php

if (!defined('ABSPATH'))
    die('Restricted Access');

if (!defined('MJTC_ALLOWED_TAGS')) {
    define('MJTC_ALLOWED_TAGS', array(
        'div'      => array(
            'class'  => array(),
            'id' => array(),
            'data-sitekey' => array(),
            'title' => array(),
            'role' => array(),
            'onclick' => array(),
            'onmouseout' => array(),
            'onmouseover' => array(),
            'data-section' => array(),
            'data-sectionid' => array(),
            'data-sitekey' => array(),
            'data-boxid' => array(),
            'data-per' => array(),
            'data-nonce' => array(),
            'style' => array(),
        ),
        'button'      => array(
            'class'  => array(),
            'id' => array(),
            'type' => array(),
            'title' => array(),
            'role' => array(),
            'data-dismiss' => array(),
            'aria-label' => array(),
            'style' => array(),
        ),
        'i'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'h1'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'h2'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'h3'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'h4'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'h5'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'h6'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),  
        ),
        'font'      => array(
            'class'  => array(),
            'id' => array(),
            'style' => array(),
        ),
        'span'      => array(
            'class'  => array(),
            'id' => array(),
            'aria-hidden' => array(),
            'style' => array(),
        ),
        'input'      => array(
            'type'  => array(),
            'id' => array(),
            'class' => array(),
            'name' => array(),
            'value' => array(),
            'onclick' => array(),
            'data-validation' => array(),
            'required' => array(),
            'size' => array(),
            'placeholder' => array(),
            'checked' => array(),
            'autocomplete' => array(),
            'multiple' => array(),
            'rel' => array(),
            'maxlength' => array(),
            'disabled' => array(),
            'readonly' => array(),
            'credit_userid' => array(),
            'data-dismiss' => array(),
            'data-validation-optional' => array(),
            'data-callback' => array(),
            'data-action' => array(),
            'data-sitekey' => array(),
            'style' => array(),
        ),
        'textarea'     => array(
            'rows' => array(),
            'name' => array(),
            'class' => array(),
            'id' => array(),
            'value' => array(),
            'cols' => array(),
            'data-validation' => array(),
            'autocomplete' => array(),
            'style' => array(),
        ),
        'button'      => array(
            'type'  => array(),
            'id' => array(),
            'class' => array(),
            'name' => array(),
            'value' => array(),
            'onclick' => array(),
            'data-validation' => array(),
            'required' => array(),
            'data-dismiss' => array(),
            'style' => array(),
        ),
        'select'      => array(
            'id' => array(),
            'class' => array(),
            'name' => array(),
            'onchange' => array(),
            'data-validation' => array(),
            'required' => array(),
            'multiple' => array(),
            'style' => array(),
        ),
        'option'      => array(
            'id' => array(),
            'class' => array(),
            'name' => array(),
            'value' => array(),
            'selected' => array(),
            'style' => array(),
        ),
        'img'      => array(
            'src'  => array(),
            'id' => array(),
            'class' => array(),
            'onclick' => array(),
            'alt' => array(),
            'width' => array(),
            'height' => array(),
            'border' => array(),
            'style' => array(),
        ),
        'link'      => array(
            'src'  => array(),
            'id' => array(),
            'rel' => array(),
            'href' => array(),
            'media' => array(),
            'style' => array(),
        ),
        'meta'      => array(
            'property'  => array(),
            'content' => array(),
            'style' => array(),
        ),
        'a'      => array(
            'href'  => array(),
            'title' => array(),
            'onclick' => array(),
            'id' => array(),
            'class' => array(),
            'name' => array(),
            'data-toggle' => array(),
            'data-id' => array(),
            'data-name' => array(),
            'data-email' => array(),
            'data-ticketid' => array(),
            'data-name' => array(),
            'data-email' => array(),
            'message' => array(),
            'confirmmessage' => array(),
            'data-for' => array(),
            'data-sortby' => array(),
            'data-image1' => array(),
            'data-image2' => array(),
            'data-gall' => array(),
            'target' => array(),
            'data-tab-number' => array(),
            'style' => array(),
        ),
        'ul'      => array(
            'type'  => array(),
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'ol'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'li'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'dl'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'dt'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'dd'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'table'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'tr'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'td'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'th'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'p'      => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'form'      => array(
            'id' => array(),
            'class' => array(),
            'method' => array(),
            'action' => array(),
            'enctype' => array(),
        ),
        'label'      => array(
            'id' => array(),
            'class' => array(),
            'for' => array(),
            'onclick' => array(),
            'style' => array(),
        ),
        'i'     => array(
            'id' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'script'     => array(
            'type' => array(),
            'class' => array(),
            'style' => array(),
        ),
        'br'     => array(
            'style' => array(),),
        'hr'     => array(
            'style' => array(),),
        'b'     => array(
            'style' => array(),),
        'em'     => array(
            'style' => array(),),
        'strong' => array(
            'style' => array(),
        ),
        'small' => array(
            'style' => array(),),
        '&nbsp' => array(),
    ));
}
define('MJTC_PLUGIN_PATH', plugin_dir_path( __DIR__ ));
define('MJTC_PLUGIN_URL', plugin_dir_url( __DIR__ ));
?>
