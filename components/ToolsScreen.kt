package com.example.mindcare.ui.screens

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.example.mindcare.ui.components.GradientHeader
import com.example.mindcare.ui.components.QuickActionCard

@Composable
fun ToolsScreen(
    innerPadding: PaddingValues,
    onOpenBreathing: () -> Unit,
    onOpenMeditation: () -> Unit,
    onOpenGrounding: () -> Unit,
    onOpenCrisisSupport: () -> Unit
) {
    LazyColumn(
        modifier = Modifier
            .fillMaxSize()
            .padding(innerPadding)
    ) {
        item {
            GradientHeader(
                title = "Your calming toolkit",
                subtitle = "Gentle practices for breath, body, and mind.",
                emoji = "🧰"
            )
        }
        item {
            Spacer(modifier = Modifier.height(16.dp))
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 20.dp)
            ) {
                Text(text = "Recommended for today")
            }
        }
        item {
            Spacer(modifier = Modifier.height(8.dp))
            Column(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(horizontal = 16.dp),
            ) {
                QuickActionCard(
                    title = "4‑7‑8 Breathing",
                    subtitle = "Slow your nervous system with guided breaths.",
                    emoji = "🌬️",
                    onClick = onOpenBreathing
                )
                Spacer(modifier = Modifier.height(8.dp))
                QuickActionCard(
                    title = "Meditation playlist",
                    subtitle = "Soft audio journeys for focus and rest.",
                    emoji = "🎧",
                    onClick = onOpenMeditation
                )
                Spacer(modifier = Modifier.height(8.dp))
                QuickActionCard(
                    title = "5‑4‑3‑2‑1 Grounding",
                    subtitle = "Return to the present with your senses.",
                    emoji = "🌱",
                    onClick = onOpenGrounding
                )
                Spacer(modifier = Modifier.height(8.dp))
                QuickActionCard(
                    title = "Crisis support",
                    subtitle = "If things feel unsafe, reach out now.",
                    emoji = "🆘",
                    onClick = onOpenCrisisSupport
                )
            }
        }
        item { Spacer(modifier = Modifier.height(24.dp)) }
    }
}


