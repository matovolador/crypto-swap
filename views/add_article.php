<script src="//cdn.ckeditor.com/4.7.1/full/ckeditor.js"></script>
<h4>Add Article</h4>
<form id="form" method="post" action="">
	<div class="form-group">
		<label>Title</label>
		<input type="text" name="title" class="form-control" id="title" required>
	</div>
	<div class="form-group">
		<label>Body</label>
		<textarea name="editor1" class="form-control"></textarea>
	</div>
</form>
<textarea name="editor1"></textarea>
<script>
    CKEDITOR.replace( 'editor1' );
</script>