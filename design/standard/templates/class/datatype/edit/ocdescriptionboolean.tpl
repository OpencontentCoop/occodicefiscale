{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<div class="block">
    <label for="ContentClass_ezboolean_default_value_{$class_attribute.id}">{'Default value'|i18n( 'design/standard/class/datatype' )}:</label>
    <input type="checkbox" id="ContentClass_ezboolean_default_value_{$class_attribute.id}" name="ContentClass_ezboolean_default_value_{$class_attribute.id}" {$class_attribute.data_int3|choose( '', 'checked="checked"' )} />
    <input type="hidden" name="ContentClass_ezboolean_default_value_{$class_attribute.id}_exists" value="1" />
</div>

<div class="block">
    <label for="ContentClass_ezboolean_text_{$class_attribute.id}">{'Text'|i18n( 'design/standard/class/datatype' )}:</label>
    <textarea id="ContentClass_ezboolean_text_{$class_attribute.id}" name="ContentClass_ezboolean_text_{$class_attribute.id}" class="summernote_ezboolean_text">{$class_attribute.data_text5|wash()}</textarea>
</div>

<div class="block">
    <label for="ContentClass_ezboolean_accept_text_{$class_attribute.id}">{'Accept string'|i18n( 'design/standard/class/datatype' )}:</label>
    <input type="text" id="ContentClass_ezboolean_accept_text_{$class_attribute.id}" name="ContentClass_ezboolean_accept_text_{$class_attribute.id}" value="{$class_attribute.data_text4|wash()}" />
</div>

{ezscript_require(array('summernote-lite.js'))}
{ezcss_require(array('summernote-lite.css'))}
{run-once}
<script>{literal}
	$(document).ready(function(){
		$('.summernote_ezboolean_text').summernote({
			toolbar: [		    
				['style', ['bold', 'italic', 'underline', 'clear']],		    		    
				['insert', ['link']]
			]
		});
	});
{/literal}</script>
{/run-once}