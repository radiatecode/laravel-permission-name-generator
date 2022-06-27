<script type="text/javascript">
    $('.head-checkbox').click(function () {
        var permission_id = $(this).attr('id');
        var isChecked = $(this).is(':checked');

        $('.' + permission_id + '-routes-checkbox ul li').each(function () {
            $(this).find(':checkbox').prop('checked', isChecked);
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

    function saveRolePermissions() {
        var permissions = checkBoxValues();

        var url = '{{ $url }}';

        if (! url){
            alert('No url found to save role permissions!');

            return 0;
        }

        if (permissions.length === 0){
            alert('No permission selected!');

            return 0;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                permissions: permissions
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function (){
                $('.save-btn').css({'cursor' : 'not-allowed'});
                $('.save-loader').removeClass('fa-save');
                $('.save-loader').addClass('fa-spinner spin');
            },
            success: function (response){
                $('.save-btn').css({'cursor' : ''});
                $('.save-loader').removeClass('fa-spinner spin');
                $('.save-loader').addClass('fa-save');
                window.location.reload();
            },
            error: function (error){
                alert('Something went wrong!');
                console.log(error);
            }
        });
    }

    function checkBoxValues() {
        var arr = $('input[name="permissions[]"]:checked').map(function () {
            return this.value;
        }).get();

        return arr;
    }
</script>
