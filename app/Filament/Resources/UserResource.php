<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\UserResource\Pages;
    use App\Filament\Resources\UserResource\RelationManagers;
    use App\Models\User;
    use Filament\Forms;
    use Filament\Forms\Components\Section;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Illuminate\Support\Facades\Hash;
    use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

    class UserResource extends Resource
    {
        protected static ?string $model = User::class;
        protected static ?string $navigationLabel = "Empleados";
        protected static ?string $navigationGroup = 'Employees Management';
        protected static ?string $navigationIcon = 'heroicon-m-user-group';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Section::make('Personal Info')
                        ->columns(2)
                        ->description('Info personal del empleado')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required(),
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->dehydrateStateUsing(fn($state) => Hash::make($state)),
                            Forms\Components\TextInput::make('password_confirmation'),
                            Forms\Components\FileUpload::make('avatar')
                                ->getUploadedFileNameForStorageUsing(
                                    fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                        ->prepend(time() . '-'),
                                )
                                ->directory('images/avatars')
                                ->avatar()
                                ->imageEditor()
                                ->circleCropper(),
                            Forms\Components\Toggle::make('is_active'),
                            /*         Forms\Components\CheckboxList::make('roles')
                                         ->relationship('roles', 'name')
                                         ->searchable(),*/
                        ]),
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\ImageColumn::make('avatar')
                        ->label('Avatar')
                        ->circular()
                        ->defaultImageUrl(function ($record) {
                            return 'https://ui-avatars.com/api/?background=random&color=fff&name=' . urlencode($record->name);
                        }),
                    Tables\Columns\TextColumn::make('name')
                        ->label('Nombre')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('email')
                        ->icon('heroicon-o-envelope')
                        ->iconColor('secondary')
                        ->searchable(),
                ])
                ->filters([
                    //
                ])
                ->actions([
                    Tables\Actions\EditAction::make()
                        ->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]);
        }

        public static function getRelations(): array
        {
            return [
                //
            ];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListUsers::route('/'),
                'create' => Pages\CreateUser::route('/create'),
                //'edit' => Pages\EditUser::route('/{record}/edit'),
            ];
        }
    }
