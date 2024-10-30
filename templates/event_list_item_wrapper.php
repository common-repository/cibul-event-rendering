<div class="cibul-event-list-item js_cibul_event_list_item">
  <?php echo $listItem; ?>
  <ul class="cibul-locations js_cibul_location">
      <?php foreach ($locations as $location): ?>
      <li>
        <span class="js_cibul_lat"><?php echo $location['latitude'] ?></span>
        <span class="js_cibul_lng"><?php echo $location['longitude'] ?></span>
        <span class="js_cibul_placename"><?php echo $location['placename'] ?></span>
        <span class="js_cibul_address"><?php echo $location['address'] ?></span>
        <span class="js_cibul_slug"><?php echo $location['slug'] ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
</div>