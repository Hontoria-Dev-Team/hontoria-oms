<?php
class PublicM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getServicesCatalog() {
        // Get all services
        $query = "SELECT id, name, isActive FROM services ORDER BY isActive DESC, name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all subservices
        $query = "SELECT id, serviceID, name, description, pricePerUnit, isActive FROM subservices ORDER BY isActive DESC, name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $subservices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all subservice images
        $query = "SELECT subserviceID, imageName FROM subserviceImages";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build image map: subserviceID => [images]
        $imageMap = [];
        foreach ($images as $image) {
            if (!isset($imageMap[$image['subserviceID']])) {
                $imageMap[$image['subserviceID']] = [];
            }
            $imageMap[$image['subserviceID']][] = [
                'imageName' => $image['imageName']
            ];
        }

        // Build subservice map: serviceID => [subservices]
        $subserviceMap = [];
        foreach ($subservices as $subservice) {
            $serviceID = $subservice['serviceID'];
            if (!isset($subserviceMap[$serviceID])) {
                $subserviceMap[$serviceID] = [];
            }

            $subserviceMap[$serviceID][] = [
                'name' => $subservice['name'],
                'description' => $subservice['description'],
                'pricePerUnit' => $subservice['pricePerUnit'],
                'isActive' => $subservice['isActive'],
                'images' => $imageMap[$subservice['id']] ?? []
            ];
        }

        // Build final catalog: services with their subservices
        $catalog = [];
        foreach ($services as $service) {
            $catalog[] = [
                'name' => $service['name'],
                'isActive' => $service['isActive'],
                'subservices' => $subserviceMap[$service['id']] ?? []
            ];
        }

        return $catalog;
    }
}
