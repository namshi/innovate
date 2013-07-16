<form name="acsform" action="[acsurl obtained from the MPI request]" method="post">
    <input type="hidden" name="PaReq" value="[The pareq data from the MPI request]">
    <input type="hidden" name="MD" value="[Optional transaction reference]">
    <input type="hidden" name="TermUrl" value="[return URL on your site]">
    <noscript><input type="Submit"></noscript>
</form>

<script>
    function autosub() {
        document.forms['acsform'].submit();
    }
    document.onload=autosub;
</script>
