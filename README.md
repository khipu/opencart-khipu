# Integración Opencart-Khipu

## Usar khipu como medio de pago

Este plugin ofrece integración del sistema de e-commerce [opencart](http://www.opencart.com/) con [khipu](https://khipu.com). 
Al instalarlo permite a los clientes pagar usando *Transferencia simplificada* (usando el terminal de pago) o con *Transferencia electrónica normal*.
En la administración del portal se puede definir si ambas opciones o una sola estarán disponibles.

## Instalación

Puedes revisar una [guía online](https://khipu.com/page/opencart) de como instalar este plugin.

Previo a activar la extensión de khipu debes ejecutar los siguientes pasos:

- Crear la moneda "Peso Chileno" de código CLP.
- Configurar tu sitio para usar esta moneda por omisión.

Luego debes ir a la configuración de las extensiones y activar _Transferencia simplificada_ y/o _Transferencia electrónica normal_. 

En la configuración de cada extensión debes incluir tu *id de cobrador* y tu *llave de cobrador*. Estas las puedes obtener de
las opciones de tu cuenta de cobro en el portal de khipu.

## Empaquetar la extensión

Esta extensión utiliza [lib-php](https://github.com/khipu/lib-php) para la comunicación con khipu. Antes de empaquetar es encesario que 
actualices los submodulos del proyecto ejecutando:

$ git submodule update --init

Luego debes ejecutar el shell-script que se incluye:

$ ./package.sh

Luego de esto, se genera el archivo _dist/khipu.ocmod.zip_.

## Como reportar problemas o ayudar al desarrollo

El sitio oficial de esta extensión es su [página en github.com](https://github.com/khipu/opencart-khipu). Si deseas 
informar de errores, revisar el código fuente o ayudarnos a mejorarla puedes usar el sistema de tickets y pull-requests. Toda ayuda es bienvenida.

## licencia GPL

Este plugin se distribuye bajo los términos de la licencia GPL versión 3. Puedes leer el archivo license.txt con los detalles de la licencia.
