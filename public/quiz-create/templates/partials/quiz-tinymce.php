<?php // tinymce selector configuration: https://www.tiny.cloud/docs/general-configuration-guide/basic-setup/#selectorconfiguration 
?>
<h1>Testing</h1>
<script>
  tinymce.init({
    selector: 'textarea#enp-question-explanation__<?php echo $question_id; ?>',
    plugins: 'link',
    menubar: 'insert',
    toolbar: 'link',
    height: 300
  });
</script>