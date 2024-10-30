<?php if ($updated): ?>
<div class="updated"><p><strong><?php echo __('Settings updated') ?></strong></p></div>
<?php endif; ?>
<?php if ($cache_clear_message): ?>
<div class="updated"><p><strong><?php echo __('Cache has been cleared') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
  <div id="icon-options-general" class="icon32"><br></div>
  <h2>Cibul Plugin Settings</h2>
  
  <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row" colspan="2">
          <h3>Key</h3>
        </th>
        <td rowspan="4">
          <?php if (!$options['keyValid']): ?>
          <iframe height="300" width="400" src="https://cibul.net/settings/api/show?external="></iframe>
          <?php endif; ?>
        </td>
      </tr>
      <?php if (!$options['keyValid']): ?>
      <tr valign="top">
        <th scope="row" colspan="2">
          
          <p><?php echo __('To enable the plugin and enable it to fetch event data, you need to register a valid api key. Getting one is free, you only need a Cibul account. Use the menu on the right to connect to Cibul to retrieve your key, or click on the Create account link to make yourself an account. Once you have your key, copy it in the field below.') ?></p>
          
        </th>
      </tr>
      <?php endif; ?>
      <tr valign="top">
        <th scope="row">
          <label>Key:</label>
        </th>
        <td>
          <input type="text" class="regular-text" id="cibul_options_key" name="cibul_key" value="<?php echo $options['key'] ?>" />
          <?php if ($options['keyValid']): ?>
          <div><?php echo __('Key is valid.'); ?></div>
          <?php else: ?>
          <?php if (strlen($options['key'])): ?>
          <div><?php echo __('Key is not valid.'); ?></div>
          <?php endif; ?>
          <?php endif; ?>
          <span class="submit"><input name="update_cibul_settings" type="submit" value="<?php echo __('Update Key') ?>"/></span>
        </td>
      </tr>
    </tbody>
  </table>
  </form>

  <h3><?php echo __('Plugin Options') ?></h3>

  <?php if (!$options['keyValid']): ?>
  <p><?php echo __('Plugin will be enabled once a valid key has been input'); ?>
  <?php else: ?>

  
  <table class="form-table">
    <tbody>
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <tr valign="top">
          <th scope="row">
            <label><?php echo __('Preferred Language') ?></label>
          </th>
          <td>
            <select name="cibul_lang">
              <option value="en" <?php echo ($options['lang']=='en')?'selected="selected"':'' ?>>English</option>
              <option value="fr" <?php echo ($options['lang']=='fr')?'selected="selected"':'' ?>>Français</option>
            </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">
            <div class="submit"><input name="update_cibul_settings" type="submit" value="<?php echo __('Update') ?>"/></div>
          </th>
        </tr>
        </form>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
          <tr>
            <td colspan="2">
              <span class="submit"><input name="clear_cache" type="submit" value="<?php echo __('Clear cache') ?>"/></span>
              <span class="description"><?php echo __('Remove loaded event data. Useful if you modified an event on Cibul and want to reflect the change on wordpress') ?></label>
            </td>
          </tr>
        </form>
    </tbody>
  </table>

  <h3><?php echo __('Advanced Options - Event item layout'); ?></h3>
  
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row" colspan="2">
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
            <p><?php echo __('Customize Event item css') ?>:</p>    
            <textarea name="cibul_event_list_item_css" cols="120" rows="12"><?php echo $options['templates']['event-list-item.css'] ?></textarea>
            <input type="hidden" class="js_default_value" value="<?php echo htmlspecialchars($default_templates['event-list-item.css']) ?>"/>
            <div><a href="#" class="js_restore_default"><?php echo __('Restore default') ?></a></div>
            <div class="submit"><input type="submit" name="update_cibul_list_item_css" value="<?php echo __('Update css') ?>"/></div>
          </form>
        </th>
      </tr>

      <tr valign="top">
        <th scope="row">
          <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
            <p><?php echo __('Customize Event item html') ?>:</p>
            <textarea name="cibul_event_list_item_template" cols="120" rows="12"><?php echo $options['templates']['event-list-item.html'] ?></textarea>
            <input type="hidden" class="js_default_value" value="<?php echo htmlspecialchars($default_templates['event-list-item.html']) ?>"/>
            <div><a href="#" class="js_restore_default"><?php echo __('Restore default') ?></a></div>
            <div class="submit"><input type="submit" name="update_cibul_list_item_template" value="<?php echo __('Update template') ?>"/></div>            
          </form>
        </th>
        <td>
          <p><?php echo __('Event item template keywords') ?></p>
          <ul>
            <li>
              <span>{% if hasImage == true %}</span> - <span class="description"><?php echo __('test existence of event image') ?></span>
            </li>
            <li>
              <span>{{ link }}</span> - <span class="description"><?php echo __('url of the event page on cibul.net') ?></span>
            </li>
            <li>
              <span>{{ description }}</span> - <span class="description"><?php echo __('Description of the event') ?></span>
            <li>
            <li>
              <span>{{ spacetimeinfo }}</span> - <span class="description"><?php echo __('Information about dates and locations') ?></span>
            <li>
            <li>
              <span>{{ freeText }}</span> - <span class="description"><?php echo __('Any further details concerning the event') ?></span>
            <li>
            <li>
              <span>{{ imageThumb }}</span> - <span class="description"><?php echo __('Thumbnail of the event') ?></span>
            <li>
            <li>
              <span>{{ image }}</span> - <span class="description"><?php echo __('Image of the event') ?></span>
            <li>
            <li>
              <span>{{ mapIconClass }}</span> - <span class="description"><?php echo __('allows the Map widget to set icon references in the element where this selector is set') ?></span>
            </li>
          </ul>
          <p><a href="http://twig.sensiolabs.org"><?php echo __('The template engine used is Twig') ?></a></p>
        </td>
      </tr>
    </tbody>
  </table>

  <?php endif; ?>

</div>