<x-mail::message>
# Olá! {{$user->name}}

Você está recebendo esse email, porque solicitou recuperar senha. 

Se você realmente solicitou recuperar senha, clique no botão abaixo para recuperar senha ou copie o link no navegador.

{{$url}}

<x-mail::button :url="'{{$url}}'">
Recuperar senha
</x-mail::button>

**Caso você não tenha solicitado, basta ignorar esse email**

Obrigado,
Equipe Snap Oral Cancer!
</x-mail::message>
