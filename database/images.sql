CREATE TABLE `images`
(
  `id`     int(9) NOT NULL,
  `name`   varchar(255) NOT NULL,
  `image`  longblob     NOT NULL,
  `userID` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;