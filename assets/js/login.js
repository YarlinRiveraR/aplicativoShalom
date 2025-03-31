const btnRegister = document.querySelector("#btnRegister");
const btnLogin = document.querySelector("#btnLogin");
const frmLogin = document.querySelector("#frmLogin");
const frmRegister = document.querySelector("#frmRegister");
const registrarse = document.querySelector("#registrarse");
const login = document.querySelector("#login");

const nombreRegistro = document.querySelector("#nombreRegistro");
const claveRegistro = document.querySelector("#claveRegistro");
const correoRegistro = document.querySelector("#correoRegistro");

const correoLogin = document.querySelector("#correoLogin");
const claveLogin = document.querySelector("#claveLogin");

const inputBusqueda = document.querySelector("#search");

//NEW!!!
const btnForgot = document.querySelector("#btnForgot");
const frmRecuperarPass = document.querySelector("#frmRecuperarPass");
const btnVolverLogin = document.querySelector("#btnVolverLogin");
const btnRecuperar = document.querySelector("#btnRecuperar");
const correoRecuperar = document.querySelector("#correoRecuperar");
const frmRecuperarNewPass = document.querySelector("#frmRecuperarNewPass");
const btnRecuperarNew = document.querySelector("#btnRecuperarNew");

document.addEventListener("DOMContentLoaded", function () {
  btnRegister.addEventListener("click", function () {
    frmLogin.classList.add("d-none");
    frmRegister.classList.remove("d-none");
  });
  btnLogin.addEventListener("click", function () {
    frmRegister.classList.add("d-none");
    frmLogin.classList.remove("d-none");
  });
  //NEW!!!
  btnForgot.addEventListener("click", function (e) {
    frmLogin.classList.add("d-none");
    frmRegister.classList.add("d-none");
    frmRecuperarPass.classList.remove("d-none");
    frmRecuperarNewPass.classList.add("d-none");
  });
  btnVolverLogin.addEventListener("click", function(e) {
    frmRecuperarPass.classList.add("d-none");
    frmRecuperarNewPass.classList.add("d-none");
    frmLogin.classList.remove("d-none");
  });

  //registro
  registrarse.addEventListener("click", function () {
    if (
      nombreRegistro.value == "" ||
      correoRegistro.value == "" ||
      claveRegistro.value == ""
    ) {
      Swal.fire("Aviso?", "TODO LOS CAMPOS SON REQUERIDOS", "warning");
    } else {
      let formData = new FormData();
      formData.append("nombre", nombreRegistro.value);
      formData.append("clave", claveRegistro.value);
      formData.append("correo", correoRegistro.value);
      const url = base_url + "clientes/registroDirecto";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(formData);
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          Swal.fire("Aviso?", res.msg, res.icono);
          if (res.icono == "success") {
            setTimeout(() => {
              enviarCorreo(correoRegistro.value, res.token);
            }, 2000);
          }
        }
      };
    }
  });
  //login directo
  login.addEventListener("click", function () {
    if (correoLogin.value == "" || claveLogin.value == "") {
      Swal.fire("Aviso?", "TODO LOS CAMPOS SON REQUERIDOS", "warning");
    } else {
      let formData = new FormData();
      formData.append("correoLogin", correoLogin.value);
      formData.append("claveLogin", claveLogin.value);
      const url = base_url + "clientes/loginDirecto";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(formData);
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          Swal.fire("Aviso?", res.msg, res.icono);
          if (res.icono == "success") {
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          }
        }
      };
    }
  });

  //NEW!!!
  // Procesar solicitud de recuperación (enviar correo)
  btnRecuperar.addEventListener("click", function(e) {
    e.preventDefault();
    if (correoRecuperar.value.trim() === "") {
      Swal.fire("Aviso?", "El correo es requerido", "warning");
    } else {
      let formData = new FormData();
      formData.append("email", correoRecuperar.value.trim());
      const url = base_url + "clientes/sendRecovery";
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(formData);
      http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          const res = JSON.parse(this.responseText);
          Swal.fire("Aviso?", res.msg, res.icono);
          if (res.icono === "success") {
            // Opcional: Si deseas, puedes cerrar el modal o redirigir al login
            setTimeout(() => {
              window.location.reload();
            }, 2000);
          }
        }
      };
    }
  });

const urlParams = new URLSearchParams(window.location.search);
const resetToken = urlParams.get('resetToken');
if (resetToken) {
  // Abrir el modal de login y mostrar frmRecuperarNewPass
  $('#modalLogin').modal('show');
  frmLogin.classList.add("d-none");
  frmRegister.classList.add("d-none");
  frmRecuperarPass.classList.add("d-none");
  frmRecuperarNewPass.classList.remove("d-none");
}

  //procesar el restablecimiento de nueva contraseña
  btnRecuperarNew.addEventListener("click", function(e) {
    e.preventDefault();
    const newPass = document.querySelector("#new_password").value.trim();
    const confirmPass = document.querySelector("#confirm_password").value.trim();

    const urlParams2 = new URLSearchParams(window.location.search);
    const resetToken2 = urlParams2.get('resetToken');
    if(newPass === "" || confirmPass === "") {
      Swal.fire("Aviso?", "Todos los campos son requeridos", "warning");
    } else if(newPass !== confirmPass) {
      Swal.fire("Aviso?", "Las contraseñas no coinciden", "warning");
    } else {
      let formData = new FormData();
      formData.append("new_password", newPass);
      formData.append("confirm_password", confirmPass);
      const url = base_url + "clientes/resetPassword/" + resetToken2;
      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(formData);
      http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          const res = JSON.parse(this.responseText);
          Swal.fire("Aviso?", res.msg, res.icono);
          if (res.icono === "success") {
            setTimeout(() => {
              window.location.href = base_url;
            }, 2000);
          }
        }
      }
    }
  });

  //busqueda de productos
  inputBusqueda.addEventListener("keyup", function (e) {
    if (e.target.value != "") {
      const url = base_url + "principal/busqueda/" + e.target.value;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          let html = "";
          res.forEach((producto) => {
            //NEW!!!
            let precioFormateado = formatearPeso(parseFloat(producto.precio));
            html += `<div class="col-12 col-md-4 mb-4">
                    <div class="card h-100">
                      <a href="#">
                        <img src="${producto.imagen}" class="card-img-top" alt="${producto.nombre}">
                      </a>
                      <div class="card-body">
                        <a href="#" class="h2 text-decoration-none text-dark">${producto.nombre}</a>
                        <p class="card-text">
                        ${precioFormateado}
                        </p>
                        <div class="buy_bt"><a href="#" onclick="agregarCarrito(${producto.id}, 1)">Añadir</a></div>
                      </div>
                    </div>
                  </div>`;
          });
          document.querySelector("#resultBusqueda").innerHTML = html;
        }
      };
    }else{
        document.querySelector('#resultBusqueda').innerHTML = '';
    }
  });
});

function enviarCorreo(correo, token) {
  let formData = new FormData();
  formData.append("token", token);
  formData.append("correo", correo);
  const url = base_url + "clientes/enviarCorreo";
  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(formData);
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);
      Swal.fire("Aviso?", res.msg, res.icono);
      if (res.icono == "success") {
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      }
    }
  };
}

function abrirModalLogin() {
  $('#modalCarrito').modal('hide')
  $('#modalLogin').modal('show')
}
