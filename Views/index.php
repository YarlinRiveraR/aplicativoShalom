<?php include_once 'Views/template/header-principal.php'; ?>


</div>
<!-- banner bg main end -->

<!-- fashion section start -->
<?php foreach ($data['categorias'] as $categoria) { ?>
  <div class="fashion_section">
    <div class="container" id="categoria_<?php echo $categoria['id']; ?>">
      <h1 class="fashion_taital text-uppercase"><?php echo $categoria['categoria']; ?></h1>
      <p><?php echo $categoria['descripcion']; ?></p>
      <div class="row <?php echo (count($categoria['productos']) > 0) ? 'multiple-items' : ''; ?>">
        <?php foreach ($categoria['productos'] as $producto) { ?>  
          <div class="<?php echo (count($categoria['productos']) > 2) ? 'col-lg-4' : 'col-lg-12'; ?>">
          <div class="card-prod mb-4 product-wap rounded-0">
          <div class="box_main card">
              <div class="text-center">
                <img data-lazy="<?php echo BASE_URL . $producto['imagen']; ?>" />
              </div>
              <!-- <div class="btn_main"> -->
                  <div class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                    <ul class="list-unstyled">
                      <li><a class="btn btn-success text-white mt-2" href="<?php echo BASE_URL . 'principal/detail/' . $producto['id']; ?>">
                        <i class="fas fa-eye"></i></a>
                    </li>
                    </ul>
                  </div>
              <!-- </div> -->
            </div>
            <div class="box_main card-body">
              <h4 class="shirt_text"><?php echo $producto['nombre']; ?></h4>
              <p class="text-center mb-0"><span style="color: #262626;"> <?php echo $producto['precio'] . ' ' . MONEDA; ?></span></p>
            </div>
          </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>

<style>
  .card-prod {
    position: relative;
    height: 350px; /* Ajustar según sea necesario */
    overflow: hidden;
    transition: transform 0.3s ease; /* Transición suave al hacer hover */
  }

  .card-prod:hover {
    transform: scale(1.02); /* Efecto de aumento al hacer hover */
  }

  .product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0; /* Inicialmente oculto */
    transition: opacity 0.3s ease; /* Transición suave */
    z-index: 1; /* Asegura que el overlay esté por encima del contenido */
  }

  .card-prod:hover .product-overlay {
    opacity: 1; /* Mostrar el overlay al hacer hover */
  }

  .card-prod img {
    max-height: 200px; /* Ajustar según sea necesario */
    object-fit: contain;
    width: 100%;
  }

  .card-prod .card-body {
    height: 100px; /* Ajustar según sea necesario */
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
    .fashion_taital{
      
      padding-top: 20px;
    
  }
</style>


<?php include_once 'Views/template/footer-principal.php'; ?>
<script src="<?php echo BASE_URL . 'assets/js/helper.js'; ?>"></script>

<script>
  $('.multiple-items').slick({
    lazyLoad: 'ondemand',
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    responsive: [{
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
</script>

</body>

</html>