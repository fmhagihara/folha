<?php
// Display Response
if (session()->has('msg') || !isset($msgok)) : ?>
  <br>
  <div class="col-md-6 col-12 alert <?= session()->getFlashdata('alert-class') ?>">
    <?= session()->getFlashdata('msg') ?>
  </div>
<?php endif ?>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url()?>scripts.js"></script>
</body>