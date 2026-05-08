jQuery(document).ready(function(n){
    jQuery('.specialClass').closest("div.mjtc-form-custm-flds-wrp").removeClass('visible');
    jQuery('.specialClass').closest("div.mjtc-support-from-field-wrp").removeClass('visible');
});
var positionToasts = function() {
            var currentTop = 40; // Starting top position
            var gap = 10; // Space between each toast
            
            jQuery('.mjtc-toast-wrapper').each(function() {
                var $wrapper = jQuery(this);
                var $innerMsg = $wrapper.find('.mjtc-toast-msg');
                
                // .outerHeight() safely grabs the height including padding/borders
                var height = $wrapper.outerHeight() || $innerMsg.outerHeight() || 0;
                
                // Only adjust if we have a valid height calculated
                if (height > 0) {
                    $wrapper.css('top', currentTop + 'px');
                    currentTop += height + gap;
                }
            });
        };

        jQuery(document).ready(function(n) {
            // Initial positioning on ready
            positionToasts();

            // Handle all toast notifications
            jQuery('.mjtc-toast-msg').each(function() {
                var $toast = jQuery(this);
                var $wrapper = $toast.closest('.mjtc-toast-wrapper');

                // 1. Function to hide and remove the toast
                var hideToast = function() {
                    $toast.removeClass('show');
                    // Wait for the CSS fade-out transition before removing from DOM
                    setTimeout(function() {
                        $wrapper.remove();
                    }, 500);
                };

                // 2. Set the 8-second auto-hide timer
                var autoHideTimer = setTimeout(hideToast, 8000);

                // 3. Optional: Pause timer if user hovers over the notification
                $toast.hover(
                    function() { clearTimeout(autoHideTimer); }, // Pause
                    function() { autoHideTimer = setTimeout(hideToast, 3000); } // Resume with grace period
                );
            });

            // 4. Manual Close button functionality
            // Using delegation to ensure it works even for AJAX-loaded toasts
            jQuery(document).on('click', '.mjtc-toast-close', function() {
                var $msg = jQuery(this).closest('.mjtc-toast-msg');
                var $wrap = jQuery(this).closest('.mjtc-toast-wrapper');
                
                $msg.removeClass('show');
                setTimeout(function() {
                    $wrap.remove();
                }, 500);
            });
        });

        // 5. Fallbacks and Observers to keep positions updated
        jQuery(window).on('load', positionToasts);
        setTimeout(positionToasts, 50);
        setTimeout(positionToasts, 200);

        // Watch for elements being added or removed from the DOM
        var observer = new MutationObserver(function() {
            requestAnimationFrame(positionToasts);
        });
        
        observer.observe(document.body, { 
            childList: true, 
            subtree: true 
        });

function MJTC_fillSpaces(string){
    string = string.replace(" ", "%20");
    return string;
}

function MJTC_getDataForDepandantField(wpnonce, parentf, childf, type) {
    if (type == 1) {
        var val = jQuery("select#" + parentf).val();
    } else if (type == 2) {
        var val = jQuery("input[name=\'" + parentf + "\']:checked").val();
    }

    jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'fieldordering', task: 'DataForDepandantField', fvalue: val, child: childf, '_wpnonce':wpnonce}, function (data) {
        if (data) {
            var d = jQuery.parseJSON(data);
            jQuery("select#" + childf).replaceWith(MJTC_msDecodeHTML(d));
        }
    });
}

function MJTC_getDataForVisibleField(wpnonce, val, fieldname, conditionGroups) {
    // var childs = fieldname.split(",");
    var field_type = 'required';
    var finalShow = false;

    jQuery.each(conditionGroups, function(conditionGroupIndex, conditionGroup) {
        jQuery.each(conditionGroup, function(orConditionIndex, orCondition) {
            var childs = orCondition[0].visibleParentField.split(",");
            // old code start
            jQuery.each(childs, function(childi, childf) {
                var type = jQuery('[name="'+childf+'"]').attr("type");

                // Reset the field value
                if (type == 'text' || type == 'email' || type == 'password' || type == 'file') {
                    jQuery('[name="'+childf+'"]').val('');
                } else if (type == 'checkbox') {
                    jQuery('[name="'+childf+'[]"]').prop('checked', false);
                    jQuery('[name="'+childf+'"]').prop('checked', false);
                } else if (type == 'radio') {
                    jQuery('[name="'+childf+'"]').prop('checked', false);
                } else if (jQuery('[name="'+childf+'"]').hasClass("mjtc-support-custom-textarea")) {
                    jQuery('[name="'+childf+'"]').val("");
                } else if (jQuery('[name="'+childf+'"]').hasClass("mjtc-support-custom-select")) {
                    jQuery('[name="'+childf+'"]').prop('selectedIndex', 0);
                } else {
                    if (jQuery('[name="'+childf+'[]"]').attr("type") == 'checkbox') {
                        field_type = 'notRequired';
                    }
                    type = "checkboxOrMultiple";
                    if (jQuery('[name="'+childf+'[]"]').attr("multiple")) {
                        jQuery('[name="'+childf+'[]"]').children().prop('selected', false);
                        jQuery('[name="'+childf+'[]"]').prop('selectedIndex', 0);
                    } else {
                        jQuery('[name="'+childf+'[]"]').prop('checked', false);
                    }
                }

                if (val.length != 0) {
                    if (conditionGroups.hasOwnProperty(childf)) {
                        var conditionsArray = conditionGroups[childf];
                        // code start
                        finalShow = false; // Will become true if all groups pass

                        if (conditionsArray.length > 0) {
                            var allGroupsPass = true;

                            jQuery.each(conditionsArray, function(groupIndex, group) {
                                var groupPass = false; // Assume group fails unless a condition is true

                                jQuery.each(group, function(conditionIndex, condition) {
                                    console.log(condition);
                                    var result = false;
                                    var isUserField = condition.visibleParent.indexOf('ufield_') !== -1;
                                    let selector;

                                    if (condition.visibleCondition === "1" || condition.visibleCondition === "0") {
                                        // Select field
                                        selector = isUserField
                                            ? "select#" + condition.visibleParent
                                            : "select#" + condition.visibleParent + "id";

                                        $MJTC_field = jQuery(selector);
                                        // If not found, fallback to checkbox group selector
                                        if ($MJTC_field.length === 0) {
                                            $MJTC_field = jQuery("input[type='checkbox'][id^='" + condition.visibleParent + "_']");
                                        }
                                        // If not found, fallback to radiobutton group selector
                                        if ($MJTC_field.length === 0) {
                                            $MJTC_field = jQuery("input[type='radio'][id^='" + condition.visibleParent + "_']");
                                        }
                                        // If not found, fallback to multiselect group selector
                                        if ($MJTC_field.length === 0) {
                                            $MJTC_field = jQuery("select[id^='" + condition.visibleParent + "[]']");
                                        }
                                        // If not found, fallback to multiselect group selector
                                        if ($MJTC_field.length === 0) {
                                            $MJTC_field = false;
                                        }
                                        
                                        let fieldval = null;

                                        if ($MJTC_field. length > 0) {
                                            var tag = $MJTC_field.prop("tagName").toLowerCase();
                                            var type = $MJTC_field.attr("type");

                                            if (tag === "select") {
                                                // Handles both single and multi-select dropdowns
                                                var isMultiSelect = $MJTC_field.prop("multiple") === true;
                                                if (isMultiSelect) {
                                                    fieldval = [];
                                                    $MJTC_field.find("option:selected").each(function () {
                                                        fieldval.push(this.value);
                                                    });
                                                } else {
                                                    fieldval = $MJTC_field.val(); // jQuery returns array for multi-select
                                                }
                                            } else if (type === "checkbox") {
                                                // Handle checkbox group (collect all checked values)
                                                fieldval = [];
                                                $MJTC_field.filter(":checked").each(function () {
                                                    fieldval.push(this.value);
                                                });
                                            } else if (type === "radio") {
                                                // Handle radio button group
                                                fieldval = jQuery("input[name='" + condition.visibleParent + "']:checked").val();
                                            } else {
                                                // Fallback for other input types
                                                fieldval = $MJTC_field.val();
                                            }
                                        }

                                        if (condition.visibleCondition === "1") {
                                            result = Array.isArray(fieldval) 
                                                ? fieldval.includes(condition.visibleValue) 
                                                : fieldval == condition.visibleValue;
                                        } else {
                                            if (Array.isArray(fieldval)) {
                                                // Prevent condition from being true when nothing is selected
                                                result = fieldval.length > 0 && !fieldval.includes(condition.visibleValue);
                                            } else {
                                                result = fieldval != condition.visibleValue;
                                            }
                                        }
                                    } else if (condition.visibleCondition === "2" || condition.visibleCondition === "3") {
                                        // Input field (no 'id' suffix regardless of isUserField)
                                        selector = "#" + condition.visibleParent;
                                        if (selector == '#fullname') {
                                            selector = '.majestic-support-form #name';
                                        }
                                        fieldval = jQuery(selector).val();

                                        if (fieldval !== undefined && fieldval !== null) {
                                            let fieldvalLower = decodeStoredValue(fieldval).toLowerCase();
                                            let valueLower = decodeStoredValue(condition.visibleValue).toLowerCase();
                                            
                                            if (condition.visibleCondition === "2") {
                                                result = fieldvalLower.indexOf(valueLower) !== -1;  // contains
                                            } else {
                                                result = fieldvalLower.indexOf(valueLower) === -1;  // does not contain
                                            }
                                        } else {
                                            result = false;
                                        }

                                    } else {
                                        result = false; // default/fallback
                                    }

                                    // Since inside a group we want OR relation
                                    if (result) {
                                        groupPass = true; // If any condition passes, the group passes
                                        return false; // Break inner loop
                                    }
                                });

                                // If any group fails, final result is false
                                if (!groupPass) {
                                    allGroupsPass = false;
                                    return false; // Break outer loop
                                }
                            });

                            finalShow = allGroupsPass;
                        }

                        // Based on finalShow, show or hide the field

                        if (finalShow) {
                            if (type == 'checkboxOrMultiple') {
                                jQuery('[name="'+childf+'[]"]').closest("div.mjtc-form-custm-flds-wrp").removeClass('visible');
                                jQuery('[name="'+childf+'[]"]').closest("div.mjtc-support-from-field-wrp").removeClass('visible');
                            } else {
                                jQuery('[name="'+childf+'"]').closest("div.mjtc-form-custm-flds-wrp").removeClass('visible');
                                jQuery('[name="'+childf+'"]').closest("div.mjtc-support-from-field-wrp").removeClass('visible');
                            }
                            MJTC_isFieldRequired(field_type, childf, 'show', wpnonce);
                        } else {
                            if (type == 'checkboxOrMultiple') {
                                jQuery('[name="'+childf+'[]"]').closest("div.mjtc-form-custm-flds-wrp").addClass('visible');
                                jQuery('[name="'+childf+'[]"]').closest("div.mjtc-support-from-field-wrp").addClass('visible');
                            } else {
                                jQuery('[name="'+childf+'"]').closest("div.mjtc-form-custm-flds-wrp").addClass('visible');
                                jQuery('[name="'+childf+'"]').closest("div.mjtc-support-from-field-wrp").addClass('visible');
                            }
                            MJTC_isFieldRequired(field_type, childf, 'hide', wpnonce);
                        }
                        // code end
                    } else {
                        if (type == 'checkboxOrMultiple') {
                            jQuery('[name="'+childf+'[]"]').closest("div.mjtc-form-custm-flds-wrp").addClass('visible');
                            jQuery('[name="'+childf+'[]"]').closest("div.mjtc-support-from-field-wrp").addClass('visible');
                        } else {
                            jQuery('[name="'+childf+'"]').closest("div.mjtc-form-custm-flds-wrp").addClass('visible');
                            jQuery('[name="'+childf+'"]').closest("div.mjtc-support-from-field-wrp").addClass('visible');
                        }
                    }

                } else {
                    // If no value is selected, show or hide based on the field type
                    if (type == 'checkboxOrMultiple') {
                        jQuery('[name="'+childf+'[]"]').closest("div.mjtc-form-custm-flds-wrp").addClass('visible');
                        jQuery('[name="'+childf+'[]"]').closest("div.mjtc-support-from-field-wrp").addClass('visible');
                    } else {
                        jQuery('[name="'+childf+'"]').closest("div.mjtc-form-custm-flds-wrp").addClass('visible');
                        jQuery('[name="'+childf+'"]').closest("div.mjtc-support-from-field-wrp").addClass('visible');
                    }
                    MJTC_isFieldRequired(field_type, childf, 'hide', wpnonce);
                }
            });
        });
    });
}

function decodeStoredValue(encoded) {
    try {
        // Step 1: Decode HTML entities like &quot;
        const textarea = document.createElement("textarea");
        textarea.innerHTML = encoded;
        let decoded = textarea.value;

        // Step 2: Decode \u4f60\u597d to real characters
        // Wrap in double quotes and parse
        decoded = JSON.parse('"' + decoded.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"');

        return decoded;
    } catch (e) {
        return encoded; // fallback
    }
}

function MJTC_deleteCutomUploadedFile (field1) {
    jQuery("input#"+field1).val(1);
    jQuery("span."+field1).hide();
    
}

function MJTC_isFieldRequired (field_type, field, state, wpnonce) {
    jQuery.post(ajaxurl, {action: 'mjsupport_ajax', mjsmod: 'ticket', task: 'MJTC_isFieldRequired', field:field, '_wpnonce':wpnonce}, function (data) {
        if (data) {
            if (data == 1 && state == 'show' && field_type == 'required') {
                jQuery('[name="'+field+'"]').attr('data-validation', 'required');
                jQuery('[name="'+field+'[]"]').attr('data-validation', 'required');
            } else if(data == 1 && state == 'hide') {
                jQuery('[name="'+field+'"]').attr('data-validation', '');
                jQuery('[name="'+field+'[]"]').attr('data-validation', '');
            }
        }
    });
    
}

function MJTC_msDecodeHTML(html) {
    var txt = document.createElement('textarea');
    txt.innerHTML = html;
    return txt.value;
}

function jsReplyShowLoading(){
    jQuery('div#black_wrapper_ai_reply').show();
    jQuery('div#mstran_loading').css('display', 'flex');
}

function jsReplyHideLoading(){
    jQuery('div#black_wrapper_ai_reply').hide();
    jQuery('div#mstran_loading').hide();
}

function hideBoxForOtherReason(){
    jQuery('div.ms-popup-other-reason-box-wrp').slideUp('slow', function () {
        jQuery('div.ms-popup-other-reason-box').hide();
    });
}

function showBoxForOtherSingleReason(){
    jQuery('div.ms-popup-other-reason-box-wrp').slideDown('slow', function () {
        jQuery('div.ms-popup-other-reason-box').show();
    });
}

function showBoxForOtherMultipleReason(){
    jQuery('div.ms-popup-other-reason-box-wrp').slideToggle('slow', function () {
        jQuery('div.ms-popup-other-reason-box').show();
    });
}
jQuery(document).ready(function() {
    const adminMenu = jQuery('#adminmenuwrap'); 
    const myContainer = jQuery('#msadmin-wrapper, #mjtc-config-dashboard'); 
    if (adminMenu.length && myContainer.length) {
        const observer = new ResizeObserver(function(entries) {
            for (let entry of entries) {
                const menuHeight = entry.contentRect.height;
                myContainer.css('height', menuHeight + 'px');
            }
        });
        observer.observe(adminMenu[0]);
    }
});
