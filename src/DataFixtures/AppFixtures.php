<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Customer;
use App\Entity\DrivingLicense;
use App\Entity\Model;
use App\Entity\Option;
use App\Entity\Reservation;
use App\Entity\Status;
use App\Entity\Type;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // vehicle types
        $types = [
            'Berline', 'Break', 'Coupé', 'Cabriolet', 'Monospace', 'SUV', 'Utilitaire'
        ];
        $typeEntities = [];
        foreach ($types as $type) {
            $vehicleType = new Type();
            $vehicleType->setName($type);
            $typeEntities[] = $vehicleType;
        }

        // vehicle brands and models
        $brands = [
            'Citroën' => ['C1', 'C3', 'C3 Aircross', 'C4', 'C4 Cactus', 'C5 Aircross', 'Berlingo', 'C-Zero', 'Jumper', 'Jumpy', 'Spacetourer', 'Rifter'],
            'Peugeot' => ['208', '2008', '308', '3008', '5008', '508', 'Rifter'],
            'Renault' => ['Zoe', 'Twingo', 'Clio', 'Mégane', 'Scénic', 'Talisman', 'Kangoo', 'Trafic', 'Master'],
            'Toyota' => ['Kadjar', 'Captur', 'Koleos', 'C-HR', 'Yaris', 'Aygo', 'Corolla', 'RAV4', 'Land Cruiser'],
        ];
        $brandEntities = [];
        $modelEntities = [];

        // make sure that a model isn't linked to multiple brands
        foreach ($brands as $brand => $models) {
            $brandEntity = new Brand();
            $brandEntity->setName($brand);
            $brandEntities[] = $brandEntity;
            foreach ($models as $model) {
                $modelEntity = new Model();
                $modelEntity->setName($model);
                $modelEntity->setBrand($brandEntity);
                $modelEntities[] = $modelEntity;
            }
        }

        // vehicle options
        $options = [
            'Climatisation', 'GPS', 'Radar de recul', 'Caméra de recul', 'Régulateur de vitesse', 'Bluetooth', 'Sièges chauffants', 'Toit panoramique', 'Vitres teintées', 'Jantes alliage', 'Peinture métallisée', 'Attelage', 'Barres de toit', 'Coffre de toit', 'Porte-vélos', 'Chaînes à neige', 'Siège bébé', 'Siège enfant', 'Siège rehausseur', 'Pneus neige', 'Pneus chaînés', 'Pneus cloutés', 'Pneus hiver', 'Pneus été', 'Pneus 4 saisons', 'Pneus runflat', 'Pneus anti-crevaison', 'Pneus rechapés', 'Pneus rechapés neige', 'Pneus rechapés hiver', 'Pneus rechapés été', 'Pneus rechapés 4 saisons', 'Pneus rechapés runflat', 'Pneus rechapés anti-crevaison', 'Pneus rechapés cloutés', 'Pneus rechapés chaînés',
        ];
        $optionEntities = [];
        foreach ($options as $option) {
            $vehicleOption = new Option();
            $vehicleOption->setName($option);
            $optionEntities[] = $vehicleOption;
        }

        // vehicles
        $vehicleEntities = [];
        foreach ($modelEntities as $model) {
            $vehicle = new Vehicle();
            $vehicle->setModel($model);
            $vehicle->setType($typeEntities[random_int(0, count($typeEntities) - 1)]);
            $vehicle->setPassengers(random_int(2, 9));
            $vehicle->setDailyRent(random_int(20, 200));
            $vehicle->setOdometer(random_int(0, 300000));
            $vehicle->setLicensePlate(sprintf('%s-%s-%s', chr(random_int(65, 90)), random_int(100, 999), chr(random_int(65, 90))));
            $vehicle->setProductionYear(random_int(2000, 2022));
            $vehicle->setPicture('/image/vehicle'.random_int(1, 9).'.jpg');
            $vehicle->addOption($optionEntities[random_int(0, count($optionEntities) - 1)]);
            $vehicle->addOption($optionEntities[random_int(0, count($optionEntities) - 1)]);
            $vehicle->addOption($optionEntities[random_int(0, count($optionEntities) - 1)]);
            $vehicleEntities[] = $vehicle;
        }

        // driving licenses
        $licenses = [
            'A', 'AM', 'B', 'C', 'CE',
        ];
        $licenseEntities = [];
        foreach ($licenses as $license) {
            $licenseEntity = new DrivingLicense();
            $licenseEntity->setName($license);
            $licenseEntities[] = $licenseEntity;
        }

        // customers
        $customers = [
            ['John', 'Doe', '10 Downing Street', '00000', 'London', 'john.doe@example.biz', '0123456789'],
            ['Jane', 'Doe', '1600 Pennsylvania Avenue NW', '20500', 'Washington, D.C.', 'yes@no.org', '0123456789'],
            ['Jean', 'Dupont', '55 Rue du Faubourg Saint-Honoré', '75008', 'Paris', 'yum@dupont.fr', '+33 1 42 92 81 00'],
            ['Pierre', 'Dupont', '2 Rue de l\'Élysée', '75008', 'Paris', 'ilove@symfony.fake', '+33 1 42 92 81 00'],
        ];
        $customerEntities = [];
        foreach ($customers as $customer) {
            $customerEntity = new Customer();
            $customerEntity->setFirstName($customer[0]);
            $customerEntity->setLastName($customer[1]);
            $customerEntity->setAddress($customer[2]);
            $customerEntity->setZipCode($customer[3]);
            $customerEntity->setCity($customer[4]);
            $customerEntity->setEmail($customer[5]);
            $customerEntity->setPhoneNumber($customer[6]);
            $customerEntity->addDrivingLicense($licenseEntities[random_int(0, count($licenseEntities) - 1)]);
            $customerEntity->addDrivingLicense($licenseEntities[random_int(0, count($licenseEntities) - 1)]);
            $customerEntity->setRoles(['ROLE_USER']);
            $customerEntity->setPassword($this->passwordHasher->hashPassword($customerEntity, 'password'));
            $customerEntities[] = $customerEntity;
        }

        // admin user
        $admin = new Customer();
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $admin->setAddress('1 Admin Street');
        $admin->setZipCode('12345');
        $admin->setCity('Admin City');
        $admin->setEmail('admin@slv.local');
        $admin->setPhoneNumber('+123 456 7890');
        $admin->addDrivingLicense($licenseEntities[random_int(0, count($licenseEntities) - 1)]);
        $admin->addDrivingLicense($licenseEntities[random_int(0, count($licenseEntities) - 1)]);
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $customerEntities[] = $admin;


        // reservation status
        $statuses = [
            'en attente', 'confirmée', 'annulée',
        ];
        $statusEntities = [];
        foreach ($statuses as $status) {
            $statusEntity = new Status();
            $statusEntity->setName($status);
            $statusEntities[] = $statusEntity;
        }

        // reservations
        $reservations = [
            ['2022-01-01', '2022-01-02', 'en attente'],
            ['2022-01-03', '2022-01-04', 'en attente'],
            ['2022-01-05', '2022-01-06', 'en attente'],
            ['2022-01-07', '2022-01-08', 'en attente'],
        ];
        $reservationEntities = [];
        foreach ($reservations as $reservation) {
            $reservationEntity = new Reservation();
            $reservationEntity->setStartDate(new \DateTimeImmutable($reservation[0]));
            $reservationEntity->setEndDate(new \DateTimeImmutable($reservation[1]));
            $reservationEntity->setCustomer($customerEntities[random_int(0, count($customerEntities) - 1)]);
            $reservationEntity->setVehicle($vehicleEntities[random_int(0, count($vehicleEntities) - 1)]);
            $reservationEntity->setStatus($manager->getRepository(Status::class)->findOneBy(['name' => $reservation[2]]));
            $reservationEntity->setReference(uniqid());
            $reservationEntity->setStatus($statusEntities[0]);
            $reservationEntities[] = $reservationEntity;
        }

        // persist entities
        foreach ($typeEntities as $type) {
            $manager->persist($type);
        }
        foreach ($brandEntities as $brand) {
            $manager->persist($brand);
        }
        foreach ($modelEntities as $model) {
            $manager->persist($model);
        }
        foreach ($optionEntities as $option) {
            $manager->persist($option);
        }
        foreach ($vehicleEntities as $vehicle) {
            $manager->persist($vehicle);
        }
        foreach ($licenseEntities as $license) {
            $manager->persist($license);
        }
        foreach ($customerEntities as $customer) {
            $manager->persist($customer);
        }
        foreach ($statusEntities as $status) {
            $manager->persist($status);
        }
        foreach ($reservationEntities as $reservation) {
            $manager->persist($reservation);
        }

        $manager->flush();
    }
}
