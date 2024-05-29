-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 28 mai 2024 à 16:09
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `willcycle`
--

-- --------------------------------------------------------

--
-- Structure de la table `fonctionnemnt`
--

CREATE TABLE `fonctionnemnt` (
  `id` int(10) NOT NULL,
  `fonctionnement` text NOT NULL,
  `machineId` int(11) NOT NULL,
  `updateBy` int(11) NOT NULL,
  `updateAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `machine`
--

CREATE TABLE `machine` (
  `id` int(10) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `addedBy` int(11) NOT NULL,
  `addedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `machine`
--

INSERT INTO `machine` (`id`, `nom`, `description`, `status`, `addedBy`, `addedAt`) VALUES
(11, 'Vecoplan Shredder', 'Vecoplan is a renowned manufacturer of industrial shredders, including ones specifically designed for shredding plastic waste. Their shredders are known for their durability, efficiency, and versatility in processing various types of plastics.', 'active', 24, '2024-05-28 12:36:28'),
(12, 'ReClaimer 3000', 'The ReClaimer 3000 is a versatile recycling solution tailored for communities and municipalities. Equipped with intelligent sensors and sorting mechanisms, it efficiently processes mixed recyclables collected from households and businesses. Its compact footprint and user-friendly interface make it an ideal choice for decentralized recycling centers.	', 'active', 24, '2024-05-28 12:42:39');

-- --------------------------------------------------------

--
-- Structure de la table `methods`
--

CREATE TABLE `methods` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `methods`
--

INSERT INTO `methods` (`id`, `name`, `description`, `createdBy`, `createdAt`) VALUES
(1, 'Pyrolysis', 'Pyrolysis is a thermal decomposition process that converts plastic waste into valuable products such as liquid fuels, gases, and char. In pyrolysis, plastic waste is heated in the absence of oxygen, causing it to break down into smaller molecules. These smaller molecules can then be condensed and refined into fuels such as diesel or gasoline, or used as feedstock for chemical processes. Pyrolysis offers a way to recover energy from plastic waste while reducing its environmental impact.', 7, '2024-05-27 13:05:42'),
(2, 'Biodegradable Plastics', 'Biodegradable plastics are designed to break down naturally in the environment through biological processes, such as microbial action or enzymatic degradation. Unlike traditional plastics, which can persist in the environment for hundreds of years, biodegradable plastics degrade into simpler compounds over time, leaving behind less harmful residues. While not all biodegradable plastics are suitable for recycling in traditional recycling systems, they offer a potential solution for reducing plastic pollution in certain applications, such as food packaging and agricultural films.', 7, '2024-05-27 13:07:08');

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

CREATE TABLE `product` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Order Placement','Order Confirmation','Material Receipt','Quality Control and Sorting','Processing and Recycling','Quality Assurance','Packaging and Storage','Order Fulfillment','Shipping and Delivery','Order Completion and Invoice') NOT NULL,
  `addedBy` int(11) NOT NULL,
  `addedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `status`, `addedBy`, `addedAt`) VALUES
(12, 'faf', 'dgsfdgs', 'Order Fulfillment', 2, '2024-05-27 15:06:58'),
(14, 'awfaw', 'awfaw', 'Packaging and Storage', 20, '2024-05-28 13:04:47');

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) NOT NULL,
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `productId` int(11) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `reports`
--

INSERT INTO `reports` (`id`, `title`, `content`, `productId`, `createdBy`, `createdAt`) VALUES
(12, 'sgsgf', 'segsegwseg', 12, 18, '2024-05-28 12:32:43'),
(13, 'segse', 'segseg', 12, 18, '2024-05-28 12:32:50');

-- --------------------------------------------------------

--
-- Structure de la table `transportation`
--

CREATE TABLE `transportation` (
  `id` int(10) NOT NULL,
  `name` varchar(40) NOT NULL,
  `available` enum('available','not available','en route','') NOT NULL,
  `contact` int(10) NOT NULL,
  `capacity` varchar(50) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `transportation`
--

INSERT INTO `transportation` (`id`, `name`, `available`, `contact`, `capacity`, `createdBy`, `createdAt`) VALUES
(1, 'RM delevero', 'available', 23041375, '53', 17, '2024-05-27 11:37:18'),
(3, 'UPS', 'en route', 213975, '120', 17, '2024-05-27 11:39:28'),
(4, 'GrandeJuve', 'not available', 93725236, '60', 17, '2024-05-27 13:04:23'),
(5, 'TorinoDome', 'en route', 4567457, '17', 17, '2024-05-28 13:43:06');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` enum('admin','gestionnaire','logisticien','maintenance','operateur','technicien','ingénieur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'AhmedMohamedAli', 'itsnotasimplepassword892331', 'gestionnaire'),
(4, 'KarimZayed', 'abuliz', 'operateur'),
(5, 'AhmedOuaelBoukef', 'wael', 'admin'),
(6, 'HabitaMahmoudRaid ', 'bandarita', 'technicien'),
(7, 'SeifEddineBouchina', 'sxb', 'ingénieur'),
(14, 'WassimGhouaeci', 'wass', 'technicien'),
(17, 'HamzaOussama', 'alimasoura', 'logisticien'),
(18, 'lle', 'lle', 'technicien'),
(20, 'Ahmed kafin', 'itsnot', 'gestionnaire'),
(21, 'Said Daoud', 'agae325', 'logisticien'),
(24, 'dante', 'dante', 'maintenance'),
(25, 'Robiniho', 'itsnotasimplepassword892331', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `fonctionnemnt`
--
ALTER TABLE `fonctionnemnt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machineId` (`machineId`),
  ADD KEY `updateBy` (`updateBy`);

--
-- Index pour la table `machine`
--
ALTER TABLE `machine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addedBy` (`addedBy`);

--
-- Index pour la table `methods`
--
ALTER TABLE `methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `createdBy` (`createdBy`);

--
-- Index pour la table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addedBy` (`addedBy`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productId` (`productId`),
  ADD KEY `createdBy` (`createdBy`);

--
-- Index pour la table `transportation`
--
ALTER TABLE `transportation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `createdBy` (`createdBy`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `fonctionnemnt`
--
ALTER TABLE `fonctionnemnt`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `machine`
--
ALTER TABLE `machine`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `methods`
--
ALTER TABLE `methods`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `transportation`
--
ALTER TABLE `transportation`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `fonctionnemnt`
--
ALTER TABLE `fonctionnemnt`
  ADD CONSTRAINT `fonctionnemnt_ibfk_1` FOREIGN KEY (`machineId`) REFERENCES `machine` (`id`),
  ADD CONSTRAINT `fonctionnemnt_ibfk_2` FOREIGN KEY (`updateBy`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `machine`
--
ALTER TABLE `machine`
  ADD CONSTRAINT `machine_ibfk_1` FOREIGN KEY (`addedBy`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `methods`
--
ALTER TABLE `methods`
  ADD CONSTRAINT `methods_ibfk_1` FOREIGN KEY (`createdBy`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`addedBy`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`createdBy`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `transportation`
--
ALTER TABLE `transportation`
  ADD CONSTRAINT `transportation_ibfk_1` FOREIGN KEY (`createdBy`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
