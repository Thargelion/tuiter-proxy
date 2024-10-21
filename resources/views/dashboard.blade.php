<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xlleading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">

                <x-bladewind::table>
                    <x-slot name="header">
                        <tr>
                            <th>Tokens</th>
                            <th>Expiration</th>
                            <th>User</th>
                            <th>Actions</th>
                        </tr>
                    </x-slot>
                    <tbody>
                    @foreach($tokens as $key => $value)
                        <tr>
                            <td>{{ $value->token }}</td>
                            <td>{{ $value->expires_at }}</td>
                            <td>{{ $user->name }}</td>
                            <td>
                                <x-bladewind::button icon="trash">
                                    <a href="{{ URL::to('/user/'.$user->id.'/tokens/'.$value->id.'/delete') }}"
                                     >Delete</a>
                                </x-bladewind::button>
                                <x-bladewind::button icon="clipboard" onclick="copy('{{ $value->token }}')">️
                                    Copy
                                </x-bladewind::button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </x-bladewind::table>
                <x-bladewind::button>
                    <a href="{{ URL::to('/user/'.$user->id.'/tokens/create') }}" class="btn btn-success">Create a
                        Token</a>
                </x-bladewind::button>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function copy(value) {
        navigator.clipboard.writeText(value);
        showNotification('Token copiado', 'El token fue copiado al portapapeles', 'info');
    }
</script>
