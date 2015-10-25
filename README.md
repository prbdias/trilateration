# Trilateration

Library to determine absolute or relative locations of points by measurement of distances.

### Example
```php
require __DIR__.'/../vendor/autoload.php';

use Prbdias\Trilateration\Trilateration;

$trilateration = new Trilateration();

//Make all the measurements in miles, by default it's in Kms.
$trilateration->setMiles(true);

//Define the 3 points to make the intersection
$trilateration->setPoint1(51.740480, -1.325897, 4);
$trilateration->setPoint2(51.812820, -1.208926, 5);
$trilateration->setPoint3(51.743257, -1.229532, 2);

//Get the intersection point
$point = $trilateration->getIntersectionPoint();

echo $point['lat'].', '.$point['lng'];
```
### Result
```
51.715231560679, -1.2442316303255
```
