<?php 
use App\User;
?>
<table class="table table-bordered" id="myTable">
  <thead align="center">
    <tr>
      <th>No</th>
      <th>Name</th>
      <th>email</th>
      <th>Total</th>
      <th>Account</th>
      <th>Account Name</th>
      <th>Withdrawal method</th>
      <th>Tanggal Create</th>
      <th>Action</th>
    </tr>
  </thead>
  @if($data->count() > 0)
      @php
          $i = 0;
      @endphp
      <tbody>
        @foreach ($data as $redeem)
          <tr>
            <td>{{ ++$i }}</td>
            <td><?php 
              $user = User::find($redeem->user_id); 
              if (!is_null($user)){
                echo $user->name;
              }
            ?></td>
            <td>
            <?php 
              if (!is_null($user)){
                echo $user->email;
              }
            ?>              
            </td>
            <td>{{ $redeem->total }}</td>
            <td>{{ $redeem->account }}</td>
            <td>{{ $redeem->account_name }}</td>
            <td>{{ $redeem->withdrawal_method }}</td>
            <td class="text-center">{{ $redeem->created_at }}</td>
            <td class="text-center">
              @if($redeem->is_paid == 0) <a id="
              {{ $redeem->id }}" class="btn btn-primary btn-sm reward">Confirm user</a> @else Success @endif
              </td>
          </tr>
        @endforeach
      </tbody>
   @endif;
  </table>

<script type="text/javascript">
  $(function(){
    $('#myTable').DataTable({
        order: [],
    });   

    display_image_s3();       
  });

  function display_image_s3()
  {
    $( "body" ).on( "click", ".popup-newWindow", function()
    {
      event.preventDefault();
      window.open($(this).attr("href"), "popupWindow", "width=600,height=600,scrollbars=yes");
    });
  }
 
</script>

