<script type="text/javascript">
    $('.head-checkbox').click(function (){
       var permission_id = $(this).attr('id');
       var isChecked = $(this).is(':checked');

       $('.'+permission_id+'-routes-checkbox ul li').each(function (){
          $(this).find(':checkbox').prop('checked',isChecked);
       });
    });

    function uncheckAll() {
        $('input[type=checkbox]').each(function () {
            this.checked = false;
        });
    }

    function checkAll() {
        $('input[type=checkbox]').each(function () {
            this.checked = true;
        });
    }
</script>
