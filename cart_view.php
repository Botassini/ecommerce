<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="includes/css/cart-view.css">
<script src="includes/js/cart-view.js"></script>
<?php
$conn = $pdo->open();
if(isset($_SESSION['user'])){
	$conn = $pdo->open();

	$stmt = $conn->prepare("SELECT * FROM cart LEFT JOIN products on products.id=cart.product_id WHERE user_id=:user_id");
	$stmt->execute(['user_id'=>$user['id']]);
	$total = 0;
	foreach($stmt as $row){
		$subtotal = $row['price'] * $row['quantity'];
		$total += $subtotal;
	}

	$pdo->close();
}

if(isset($_SESSION['user'])){
	if(isset($_SESSION['cart'])){
		foreach($_SESSION['cart'] as $row){
			$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id");
			$stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid']]);
			$crow = $stmt->fetch();
			if($crow['numrows'] < 1){
				$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
				$stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid'], 'quantity'=>$row['quantity']]);
			}
			else{
				$stmt = $conn->prepare("UPDATE cart SET quantity=:quantity WHERE user_id=:user_id AND product_id=:product_id");
				$stmt->execute(['quantity'=>$row['quantity'], 'user_id'=>$user['id'], 'product_id'=>$row['productid']]);
			}
		}
		unset($_SESSION['cart']);
	}

	try{
		$total = 0;
		$stmt = $conn->prepare("SELECT *, cart.id AS cartid FROM cart LEFT JOIN products ON products.id=cart.product_id WHERE user_id=:user");
		$stmt->execute(['user'=>$user['id']]);
		foreach($stmt as $row){
			$image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
			$subtotal = $row['price']*$row['quantity'];
			$total += $subtotal;
			
		}


	}
	catch(PDOException $e){
		
	}

}
else{
	if(count($_SESSION['cart']) != 0){
		$total = 0;
		foreach($_SESSION['cart'] as $row){
			$stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname FROM products LEFT JOIN category ON category.id=products.category_id WHERE products.id=:id");
			$stmt->execute(['id'=>$row['productid']]);
			$product = $stmt->fetch();
			$image = (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg';
			$subtotal = $product['price']*$row['quantity'];
			$total += $subtotal;
	

			
		}

		
	}

	else{
		
	}
	
}

// SDK de Mercado Pago
require __DIR__ .  '/vendor/autoload.php';

// Configurar credenciais
MercadoPago\SDK::setAccessToken('APP_USR-4002014223947242-102412-31143af3104758daf0593e1e8befe1d8-740803689');

// Criar um objeto de preferencia
$preference = new MercadoPago\Preference();

// Crear un elemento en la preferencia
$item = new MercadoPago\Item();
$item->title = 'Total da compra: ';
$item->quantity = 1;
$item->unit_price = $total;
$preference->items = array($item);
$preference->external_reference = 'Pedido 1';


$preference->save();


$pdo->close();






?>

<body class="hold-transition skin-blue layout-top-nav">
<div>
	
<div class="wrap cf">
  <h1 style="display: flex; justify-content: center; font-size: 40px;">FGL Distribuidora<span></span></h1>
  <div class="heading cf">
    <h1>Meu Carrinho</h1>
    <a href="index.html" class="continue">Continue Shopping</a>
  </div>
  <div class="cart">
<!--    <ul class="tableHead">
      <li class="prodHeader">Product</li>
      <li>Quantity</li>
      <li>Total</li>
       <li>Remove</li>
    </ul>-->
    <ul class="cartWrap">
    <li class="items odd">
        
    <div class="infoWrap"> 
        <div class="cartSection">
		<div class="box box-solid">
			<div class="box-body">
				<table class="table table-bordered">
					<thead>
						<th></th>
						<th>Foto</th>
						<th>Nome</th>
						<th>Preço</th>
						<th width="20%">Quantidade</th>
						
					</thead>
					
				</table>
			</div>
		</div>
        </div>
	</div>
	</li>
      
  
  
  <div class="subtotal cf">
    <ul>
      <li class="totalRow"><span class="label">Subtotal</span><span class="value">$35.00</span></li>
      
          <li class="totalRow"><span class="label">Shipping</span><span class="value">$5.00</span></li>
      
            <li class="totalRow"><span class="label">Tax</span><span class="value">$4.00</span></li>
            <li class="totalRow final"><span class="label">Total</span><span class="value">$44.00</span></li>
      <li class="totalRow"><a href="#" class="btn continue">Checkout</a></li>
    </ul>
  </div>
</div>
	  
   
    
    
    
    
    
  
</div>
  
	  
	        		<?php
	        			if(isset($_SESSION['user'])){
	        				echo "
	        					<div class='cho-container'></div>
								
							
	        				";
	        			}
	        			else{
	        				echo "
	        					<h4>You need to <a href='login.php'>Login</a> to checkout.</h4>
	        				";
	        			}
	        		?>
	        	</div>
	        	<div class="col-sm-3">
	        		<?php include 'includes/sidebar.php'; ?>
	        	</div>
	        </div>
	      </section>
	     
	    </div>
	  </div>
						
	  
  	<?php $pdo->close(); ?>
  	<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<!-- GAMBIARRA ITEMS -->

<script>
var total = 0;
$(function(){
	$(document).on('click', '.cart_delete', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		$.ajax({
			type: 'POST',
			url: 'cart_delete.php',
			data: {id:id},
			dataType: 'json',
			success: function(response){
				if(!response.error){
					getDetails();
					getCart();
					getTotal();
				}
			}
		});
	});

	$(document).on('click', '.minus', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var qty = $('#qty_'+id).val();
		if(qty>1){
			qty--;
		}
		$('#qty_'+id).val(qty);
		$.ajax({
			type: 'POST',
			url: 'cart_update.php',
			data: {
				id: id,
				qty: qty,
			},
			dataType: 'json',
			success: function(response){
				if(!response.error){
					getDetails();
					getCart();
					getTotal();
				}
			}
		});
	});

	$(document).on('click', '.add', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var qty = $('#qty_'+id).val();
		qty++;
		$('#qty_'+id).val(qty);
		$.ajax({
			type: 'POST',
			url: 'cart_update.php',
			data: {
				id: id,
				qty: qty,
			},
			dataType: 'json',
			success: function(response){
				if(!response.error){
					getDetails();
					getCart();
					getTotal();
				}
			}
		});
	});

	getDetails();
	getTotal();

});

function getDetails(){
	$.ajax({
		type: 'POST',
		url: 'cart_details.php',
		dataType: 'json',
		success: function(response){
			$('#tbody').html(response);
			getCart();
		}
	});
}

function getTotal(){
	$.ajax({
		type: 'POST',
		url: 'cart_total.php',
		dataType: 'json',
		success:function(response){
			total = response;
		}
	});
}
</script>

<!DOCTYPE html>

	<html>

	<head>
		<title>Pagar</title>
	</head>

	<body>
		<div class="cho-container"/> 
			<script src="https://sdk.mercadopago.com/js/v2"></script>
			<script>
				// CREDENCIAIS
				const mp = new MercadoPago('APP_USR-a96c034c-6270-438e-84cd-f6465db2665b', {
					locale: 'pt-BR'
				});

				// CHECKOUT START
				mp.checkout({
					preference: {
						id: "<?php echo $preference->id; ?>"
					},
					render: {
						container: '.cho-container', //CLASSE DO BOTAO
						label: 'Pagamento', // LABEL DO BOTAO
					}
									
				});

			</script>
	</body>

	</html>


</body>
</html>